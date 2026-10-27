<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\{Moment, Rule};
use LogicException;

/**
 * Validation: the date must lie before $date, which takes what DateTimeImmutable takes ('today', '-18 years',
 * '2000-01-01'). Null passes, and so does a value that is no date at all: #[Date] reports that.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Before implements Rule
{
    public function __construct(
        public string $date,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        $moment = Moment::of($value);
        $limit = Moment::of($this->date) ?? throw new LogicException("#[Before]: '{$this->date}' is no date");

        return $moment === null || $moment < $limit;
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value should be before {date}.';
    }

    public function params(): array
    {
        return ['date' => $this->date];
    }
}
