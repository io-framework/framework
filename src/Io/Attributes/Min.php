<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/** Validation: a number must be at least $value. Null passes, and so does anything that is no number. */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Min implements Rule
{
    public function __construct(
        public int|float $value,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        return !(is_int($value) || is_float($value)) || $value >= $this->value;
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value should be {min} or more.';
    }

    public function params(): array
    {
        return ['min' => $this->value];
    }
}
