<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/** Validation: a non-null property value must be one of $values (strict comparison). */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OneOf implements Rule
{
    /** @param list<scalar> $values */
    public function __construct(
        public array $values,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        return $value === null || in_array($value, $this->values, true);
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value should be one of: {choices}.';
    }

    public function params(): array
    {
        return ['choices' => implode(', ', array_map('strval', $this->values))];
    }
}
