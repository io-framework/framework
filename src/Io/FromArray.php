<?php

namespace Io;

/**
 * An array constructor and validation inside the DTO, the older way to take a request body: the Hydrator (and
 * `#[Body(of: Dto::class)] Form $form`) does the same for a plain class. Each key that names a publicly writable
 * instance property is assigned, unknown keys are ignored, and a value that
 * doesn't fit the property's type is left out and recorded as a failure (a 422,
 * not a TypeError 500, and never cast: "false" is no true). A form's text for a
 * number or a boolean ("5", "on") is converted, a backed enum property gets the
 * case its value names, and a property typed with another FromArray DTO (or an
 * array whose #[ListOf(Dto::class)] says so) is built from the nested array. Private, protected, protected(set)/
 * private(set) and static properties are never touched: the data comes from
 * the request, and those are the DTO's own state.
 *
 * Validation is read straight off the DTO: `$dto->isValid` and `$dto->errors`
 * apply the rule attributes on every read, and `fail()` adds a failure a
 * controller found itself (a taken email, say) to the same list. Every failing
 * rule reports its message, so a field has a list of them. A writable property
 * without a default that the data left unset is "This value is required.".
 */
trait FromArray
{
    /** @var array<string, list<string>> Failures added through fail(), and what did not fit the types. */
    private array $failures = [];

    /**
     * @var array<string, list<string>> field => messages; attribute rules first, then fail() entries, required fields
     * left unset, and nested DTOs' errors ("address.city").
     */
    public array $errors {
        get => Validator::merge(
            (new Validator())->validate($this),
            $this->failures,
            Hydrator::missingRequired($this, $this->failures, $this->fromArrayExcludes()),
            Hydrator::nestedErrors($this),
        );
    }

    public bool $isValid {
        get => $this->errors === [];
    }

    /** @param array<string, mixed> $data */
    public function __construct(array $data = [])
    {
        $this->hydrateFromArray($data);
    }

    /** Records a failure the attributes can't know about; it joins the field's other messages. */
    public function fail(string $field, string $message): static
    {
        $this->failures[$field][] = $message;
        return $this;
    }

    /**
     * Split out so a using class can declare its own __construct() and still call this.
     * @param array<string, mixed> $data
     */
    protected function hydrateFromArray(array $data): void
    {
        $this->failures = Validator::merge($this->failures, Hydrator::hydrate($this, $data, $this->fromArrayExcludes()));
    }

    /**
     * Kept for code that used the trait's own conversion (io/live up to 0.1.10): the Hydrator's now.
     * @return array{bool, mixed, ?string}
     */
    private static function fitting(?\ReflectionType $type, mixed $value): array
    {
        return Hydrator::fitting($type, $value);
    }

    /**
     * Property names hydration skips (e.g. a nested DTO set some other way). A method, since a trait property's default can't be overridden.
     * @return array<int, string>
     */
    protected function fromArrayExcludes(): array
    {
        return [];
    }
}
