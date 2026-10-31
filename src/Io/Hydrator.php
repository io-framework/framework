<?php

namespace Io;

use Io\Attributes\{Ignore, ListOf, Trim};
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use WeakMap;

/**
 * Builds a DTO from request data and keeps what did not fit, for #[Body] and Form. A value is never cast: it is
 * taken when it fits the property's type, converted when it is a form's text for a number or a boolean ("5",
 * "on"), a backed enum's value or the array of a nested DTO, and otherwise reported ("false" is no true, 2.7 no
 * 2). #[Trim] trims text first.
 *
 * - A DTO with constructor parameters gets them by name, promoted ones as well; when a required one is missing or
 *   does not fit, it is built without its constructor, with the values that fit, so a form can show it again.
 * - Any other DTO is created without arguments and its public, publicly settable properties are filled; private,
 *   protected, protected(set), private(set), static and #[Ignore] ones never are.
 * - A FromArray DTO, and one whose constructor takes the array (`__construct(array $data)`), builds itself.
 *
 * What it learns about a class (the properties, their types, #[ListOf], #[Trim]) is kept for the life of the process.
 */
final class Hydrator
{
    /** @var array<class-string, array<string, array{property: ReflectionProperty, list: ?string, trim: bool}>> */
    private static array $inputs = [];

    /** @var WeakMap<object, array<string, list<string>>>|null nested DTOs it built => what did not fit them */
    private static ?WeakMap $made = null;

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<array-key, mixed> $data
     * @param bool $filled Whether a request filled it (a submit, not a GET): see Form::$filled
     * @return Form<T>
     */
    public static function form(string $class, array $data, bool $filled = true): Form
    {
        [$dto, $failures] = self::make($class, $data);
        return new Form($dto, $failures, $filled);
    }

    /**
     * The DTO and what did not fit it, by field
     *
     * @template T of object
     * @param class-string<T> $class
     * @param array<array-key, mixed> $data
     * @return array{T, array<string, list<string>>}
     */
    public static function make(string $class, array $data): array
    {
        if (self::usesFromArray($class)) {
            return [new $class($data), []];
        }
        $constructor = (new ReflectionClass($class))->getConstructor();
        if ($constructor !== null && self::takesTheArray($constructor)) {
            return [new $class($data), []];
        }
        if ($constructor !== null && $constructor->getNumberOfParameters() > 0) {
            return self::construct($class, $constructor, $data);
        }
        $dto = new $class();
        return [$dto, self::hydrate($dto, $data)];
    }

    /**
     * Fills $target's input properties from $data; returns what did not fit, by field.
     *
     * @param array<array-key, mixed> $data
     * @param array<int, string> $excludes Properties left alone
     * @return array<string, list<string>>
     */
    public static function hydrate(object $target, array $data, array $excludes = []): array
    {
        $failures = [];
        $inputs = self::inputs($target::class);
        foreach ($data as $key => $value) {
            $key = (string) $key;
            $input = $inputs[$key] ?? null;
            if ($input === null || in_array($key, $excludes, true)) {
                continue;
            }
            $property = $input['property'];
            $value = $input['trim'] && is_string($value) ? trim($value) : $value;
            [$fits, $value, $message] = self::fitting($property->getType(), $value);
            if (!$fits) {
                // Left at its default, and a 422: never cast ("false" is no true, 2.7 no 2)
                $failures[$key][] = $message ?? 'Expected a value of type ' . $property->getType() . '.';
                continue;
            }
            if (is_array($value) && $input['list'] !== null) {
                [$value, $elementFailures] = self::elements($key, $input['list'], $value);
                $failures = Validator::merge($failures, $elementFailures);
            }
            try {
                $target->$key = $value;
            } catch (\TypeError) {
                $failures[$key][] = 'Expected a value of type ' . $property->getType() . '.';
            }
        }
        return $failures;
    }

    /**
     * Everything wrong with a DTO it built: the rules of its attributes, $failures, a required property left
     * unset, and the errors of nested DTOs ("address.city", "lines.0.qty").
     *
     * @param array<string, list<string>> $failures
     * @return array<string, list<string>>
     */
    public static function errorsOf(object $dto, array $failures = []): array
    {
        if (self::usesFromArray($dto::class) && isset($dto->errors) && is_array($dto->errors)) {
            /** @var array<string, list<string>> $own */
            $own = $dto->errors;
            return Validator::merge($own, $failures);
        }
        return Validator::merge((new Validator())->validate($dto), $failures, self::missingRequired($dto, $failures), self::nestedErrors($dto));
    }

    /**
     * A publicly writable property without a default that nothing set: required, and reading it would throw. A
     * value the data had but that did not fit already failed, so it is not reported a second time.
     *
     * @param array<string, list<string>> $failures
     * @param array<int, string> $excludes
     * @return array<string, list<string>>
     */
    public static function missingRequired(object $dto, array $failures, array $excludes = []): array
    {
        $missing = [];
        foreach (self::inputs($dto::class) as $name => ['property' => $property]) {
            if (!in_array($name, $excludes, true) && !$property->isInitialized($dto) && !isset($failures[$name])) {
                $missing[$name] = ['This value is required.'];
            }
        }
        return $missing;
    }

    /**
     * The errors of nested DTOs, a property's or a list's, under "property.field" or "property.index.field".
     *
     * @return array<string, list<string>>
     */
    public static function nestedErrors(object $dto): array
    {
        $errors = [];
        foreach ((new \ReflectionObject($dto))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic() || $property->isVirtual() || !$property->isInitialized($dto)) {
                continue;
            }
            $value = $property->getValue($dto);
            foreach (is_array($value) ? $value : ['' => $value] as $index => $item) {
                if (!is_object($item)) {
                    continue;
                }
                $failures = self::$made?->offsetExists($item) ? self::$made->offsetGet($item) : null;
                if ($failures === null && !self::usesFromArray($item::class)) {
                    continue;
                }
                $prefix = $property->getName() . ($index === '' ? '' : ".$index");
                foreach (self::errorsOf($item, $failures ?? []) as $field => $messages) {
                    $errors["$prefix.$field"] = $messages;
                }
            }
        }
        return $errors;
    }

    /** Whether $class uses FromArray, itself or through a parent */
    public static function usesFromArray(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }
        foreach ([$class, ...array_values(class_parents($class) ?: [])] as $c) {
            if (in_array(FromArray::class, class_uses($c) ?: [], true)) {
                return true;
            }
        }
        return false;
    }

    /** A constructor that takes the whole array itself: `__construct(array $data = [])` */
    private static function takesTheArray(ReflectionMethod $constructor): bool
    {
        $params = $constructor->getParameters();
        if (count($params) !== 1) {
            return false;
        }
        $type = $params[0]->getType();
        return $type instanceof ReflectionNamedType && $type->getName() === 'array';
    }

    /**
     * The constructor's parameters by name; without its constructor when a required one is missing or does not fit
     *
     * @template T of object
     * @param class-string<T> $class
     * @param array<array-key, mixed> $data
     * @return array{T, array<string, list<string>>}
     */
    private static function construct(string $class, ReflectionMethod $constructor, array $data): array
    {
        $args = [];
        $failures = [];
        $broken = false;
        $classTrim = self::classTrim($class);
        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();
            if (!array_key_exists($name, $data)) {
                if (!$param->isOptional()) {
                    $failures[$name][] = 'This value is required.';
                    $broken = true;
                }
                continue;
            }
            $value = $data[$name];
            $value = self::trims($param, $classTrim) && is_string($value) ? trim($value) : $value;
            [$fits, $value, $message] = self::fitting($param->getType(), $value);
            if (!$fits) {
                $failures[$name][] = $message ?? 'Expected a value of type ' . $param->getType() . '.';
                $broken = $broken || !$param->isOptional();
                continue;
            }
            $list = ($param->getAttributes(ListOf::class)[0] ?? null)?->newInstance()->type;
            if (is_array($value) && $list !== null) {
                [$value, $elementFailures] = self::elements($name, $list, $value);
                $failures = Validator::merge($failures, $elementFailures);
            }
            $args[$name] = $value;
        }
        if (!$broken) {
            return [new $class(...$args), $failures];
        }
        // To show the form again: what fit is set, the rest stays unset
        $dto = (new ReflectionClass($class))->newInstanceWithoutConstructor();
        foreach ($args as $name => $value) {
            if (property_exists($dto, $name)) {
                (fn () => $this->$name = $value)->call($dto);
            }
        }
        return [$dto, $failures];
    }

    /**
     * The properties a request may set, with their #[ListOf] element type and whether text is trimmed
     *
     * @param class-string $class
     * @return array<string, array{property: ReflectionProperty, list: ?string, trim: bool}>
     */
    private static function inputs(string $class): array
    {
        if (isset(self::$inputs[$class])) {
            return self::$inputs[$class];
        }
        $classTrim = self::classTrim($class);
        $inputs = [];
        foreach ((new ReflectionClass($class))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic() || $property->isProtectedSet() || $property->isPrivateSet() || $property->isVirtual()
                || $property->isReadOnly() || $property->getAttributes(Ignore::class) !== []
                || in_array($property->getName(), ['errors', 'isValid', 'failures'], true)) {
                continue;
            }
            $inputs[$property->getName()] = [
                'property' => $property,
                'list' => ($property->getAttributes(ListOf::class)[0] ?? null)?->newInstance()->type,
                'trim' => self::trims($property, $classTrim),
            ];
        }
        return self::$inputs[$class] = $inputs;
    }

    private static function classTrim(string $class): bool
    {
        return class_exists($class) && ((new ReflectionClass($class))->getAttributes(Trim::class)[0] ?? null)?->newInstance()->trim === true;
    }

    private static function trims(ReflectionProperty|ReflectionParameter $member, bool $classTrim): bool
    {
        $own = ($member->getAttributes(Trim::class)[0] ?? null)?->newInstance();
        return $own !== null ? $own->trim : $classTrim;
    }

    /**
     * The elements of an array as #[ListOf] says: a DTO built from each array, a backed enum case from each value.
     * One that does not fit is left out and reported under "property.index".
     *
     * @param array<array-key, mixed> $items
     * @return array{array<array-key, mixed>, array<string, list<string>>}
     */
    private static function elements(string $name, string $class, array $items): array
    {
        if (!self::buildable($class) && !is_subclass_of($class, \BackedEnum::class)) {
            return [$items, []];
        }
        $result = [];
        $failures = [];
        foreach ($items as $index => $item) {
            [$fits, $converted, $message] = $item instanceof $class ? [true, $item, null] : self::converted($class, $item);
            if ($fits) {
                $result[$index] = $converted;
            } else {
                $failures["$name.$index"][] = $message ?? "Expected a value of type {$class}.";
            }
        }
        return [array_is_list($items) ? array_values($result) : $result, $failures];
    }

    /**
     * Whether $value fits $type as it is, or as the text a form sends for a number or a boolean, a backed enum's
     * value, or the array of a nested DTO; the value to assign then, and why it doesn't fit when there is more to
     * say than its type.
     *
     * @return array{bool, mixed, ?string}
     */
    public static function fitting(?ReflectionType $type, mixed $value): array
    {
        if ($type === null) {
            return [true, $value, null];
        }
        if ($value === null) {
            return [$type->allowsNull(), null, null];
        }
        $names = $type instanceof ReflectionNamedType ? [$type->getName()]
            : ($type instanceof ReflectionUnionType ? array_map(static fn (ReflectionType $t): string => $t instanceof ReflectionNamedType ? $t->getName() : '', $type->getTypes()) : []);
        // An intersection type: PHP's own check decides
        if ($names === []) {
            return [true, $value, null];
        }
        foreach ($names as $name) {
            $exact = match ($name) {
                'mixed' => true,
                'int' => is_int($value),
                'float' => is_float($value) || is_int($value),
                'bool', 'false', 'true' => is_bool($value) && ($name === 'bool' || $value === ($name === 'true')),
                'string' => is_string($value),
                'array', 'iterable' => is_array($value),
                default => is_object($value) && $value instanceof $name,
            };
            if ($exact) {
                return [true, $name === 'float' && is_int($value) ? (float) $value : $value, null];
            }
        }
        $failure = [false, null, null];
        foreach ($names as $name) {
            $converted = self::converted($name, $value);
            if ($converted[0]) {
                return $converted;
            }
            $failure = $converted[2] !== null ? $converted : $failure;
        }
        return $failure;
    }

    /**
     * $value as $name by conversion: form text as int/float/bool, a backed enum case by its value, a DTO from an array.
     *
     * @return array{bool, mixed, ?string}
     */
    private static function converted(string $name, mixed $value): array
    {
        if (is_subclass_of($name, \BackedEnum::class)) {
            $backing = (string) (new \ReflectionEnum($name))->getBackingType();
            $raw = $backing === 'int' && is_string($value) ? filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) : $value;
            $case = ($backing === 'int' && is_int($raw)) || ($backing === 'string' && is_string($raw)) ? $name::tryFrom($raw) : null;
            if ($case !== null) {
                return [true, $case, null];
            }
            $values = implode(', ', array_map(static fn (\BackedEnum $c): string => (string) $c->value, $name::cases()));
            return [false, null, "This value should be one of: {$values}."];
        }
        if (is_array($value) && self::buildable($name) && class_exists($name)) {
            [$dto, $failures] = self::make($name, $value);
            if (!self::usesFromArray($name)) {
                self::$made ??= new WeakMap();
                self::$made[$dto] = $failures;
            }
            return [true, $dto, null];
        }
        $converted = is_string($value) ? match ($name) {
            'int' => filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE),
            'float' => filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            default => null,
        } : null;
        return $converted !== null ? [true, $converted, null] : [false, null, null];
    }

    /** A class a DTO can be built of: concrete, no enum, no closure */
    private static function buildable(string $class): bool
    {
        if (!class_exists($class) || enum_exists($class) || $class === \Closure::class) {
            return false;
        }
        $reflection = new ReflectionClass($class);
        return $reflection->isInstantiable() && $reflection->isUserDefined();
    }
}
