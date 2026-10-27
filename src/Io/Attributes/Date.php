<?php

namespace Io\Attributes;

use Attribute;
use DateTimeImmutable;
use DateTimeInterface;
use Io\Validation\Rule;

/**
 * Validation: a string property must be a real date in $format (a date() format, 'Y-m-d' by default):
 * "2026-02-30" fails. Null passes, a DateTimeInterface always does.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Date implements Rule
{
    public function __construct(
        public string $format = 'Y-m-d',
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        if ($value === null || $value instanceof DateTimeInterface) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        $date = DateTimeImmutable::createFromFormat('!' . $this->format, $value);

        // Without the round trip, PHP turns February 30th into March 2nd
        return $date !== false && $date->format($this->format) === $value;
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value is not a valid date.';
    }

    public function params(): array
    {
        return ['format' => $this->format];
    }
}
