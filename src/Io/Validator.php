<?php

namespace Io;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Io\Validation\{Messages, Rule};

/**
 * Validates a DTO's public properties against their validation attributes: every attribute that implements
 * Rule (#[NotBlank], #[Email], #[Length], #[Regex], #[OneOf], #[SameAs], #[Date], #[Before], #[After], #[Min],
 * #[Max], #[Check], or one of the app's own). The rules per class are reflected once and cached, so validating
 * the same DTO class on every request costs no reflection.
 */
class Validator
{
    /** @var array<class-string, list<array{prop: ReflectionProperty, rules: list<Rule>}>> */
    private static array $rulesByClass = [];

    /** @return array<string, list<string>> field => the message of every rule that failed, in the order the rules are written */
    #[\NoDiscard('Ignoring the returned errors means validation never actually gets enforced.')]
    public function validate(object $dto): array
    {
        $errors = [];
        foreach (self::rulesFor($dto) as ['prop' => $prop, 'rules' => $rules]) {
            $value = $prop->isInitialized($dto) ? $prop->getValue($dto) : null;

            foreach ($rules as $rule) {
                if (!$rule->passes($value, $dto)) {
                    $errors[$prop->getName()][] = $this->message($rule->message($value), $rule->params());
                }
            }
        }
        return $errors;
    }

    /**
     * Lists of messages per field, put together: $b's messages follow $a's.
     * @param array<string, list<string>> ...$lists
     * @return array<string, list<string>>
     */
    public static function merge(array ...$lists): array
    {
        $merged = [];
        foreach ($lists as $list) {
            foreach ($list as $field => $messages) {
                $merged[$field] = [...$merged[$field] ?? [], ...$messages];
            }
        }
        return $merged;
    }

    /** @return list<array{prop: ReflectionProperty, rules: list<Rule>}> */
    private static function rulesFor(object $dto): array
    {
        $class = get_class($dto);
        if (isset(self::$rulesByClass[$class])) {
            return self::$rulesByClass[$class];
        }

        $entries = [];
        foreach ((new ReflectionClass($dto))->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $rules = array_map(
                fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
                $prop->getAttributes(Rule::class, ReflectionAttribute::IS_INSTANCEOF),
            );
            if ($rules !== []) {
                $entries[] = ['prop' => $prop, 'rules' => $rules];
            }
        }
        return self::$rulesByClass[$class] = $entries;
    }

    /**
     * Through the container-bound Messages (a plugin, e.g. i18n) when there is one, else the text with its placeholders filled in.
     * @param array<string, scalar> $params
     */
    private function message(string $text, array $params = []): string
    {
        $container = App::getInstance()?->getContainer();
        if ($container !== null && $container->isRegistered(Messages::class)) {
            return $container->get(Messages::class)->translate($text, $params);
        }
        foreach ($params as $name => $value) {
            $text = str_replace('{' . $name . '}', (string) $value, $text);
        }
        return $text;
    }
}
