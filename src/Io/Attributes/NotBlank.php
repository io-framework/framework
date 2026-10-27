<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/** Validation: the property must not be null, an empty/whitespace-only string, or an empty array. */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NotBlank implements Rule
{
    public function __construct(public ?string $message = null)
    {
    }

    public function passes(mixed $value, object $dto): bool
    {
        return !($value === null || (is_string($value) && trim($value) === '') || $value === []);
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value should not be blank.';
    }

    public function params(): array
    {
        return [];
    }
}
