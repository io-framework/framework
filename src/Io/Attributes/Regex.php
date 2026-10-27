<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/** Validation: the value must match $pattern (a full PCRE pattern including delimiters). Numbers match by their text, null passes, lists, objects and bools never match. */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Regex implements Rule
{
    public function __construct(
        public string $pattern,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        if ($value === null) {
            return true;
        }

        return (is_string($value) || is_int($value) || is_float($value)) && preg_match($this->pattern, (string) $value) === 1;
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value does not match the required format.';
    }

    public function params(): array
    {
        return [];
    }
}
