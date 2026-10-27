<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/**
 * Validation: a string property's length (mb_strlen) must be within [$min, $max]. Null passes, so an optional field
 * may have a minimum length; #[NotBlank] requires it. Any other type counts as length 0.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Length implements Rule
{
    public function __construct(
        public int $min = 0,
        public int $max = PHP_INT_MAX,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        if ($value === null) {
            return true;
        }
        $length = self::length($value);

        return $length >= $this->min && $length <= $this->max;
    }

    public function message(mixed $value): string
    {
        return $this->message ?? (self::length($value) > $this->max
            ? 'This value is too long. Maximum length is {max}.'
            : 'This value is too short. Minimum length is {min}.');
    }

    private static function length(mixed $value): int
    {
        return is_string($value) ? mb_strlen($value) : 0;
    }

    public function params(): array
    {
        return ['min' => $this->min, 'max' => $this->max];
    }
}
