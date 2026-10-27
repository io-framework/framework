<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/** Validation: a non-null property must be a syntactically valid email address. */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Email implements Rule
{
    public function __construct(public ?string $message = null)
    {
    }

    public function passes(mixed $value, object $dto): bool
    {
        return $value === null || filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value is not a valid email address.';
    }

    public function params(): array
    {
        return [];
    }
}
