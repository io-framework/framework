<?php

namespace Io\Attributes;

use Attribute;
use Io\Validation\Rule;

/** Validation: the property must equal the DTO's $property (e.g. a password confirmation). */
#[Attribute(Attribute::TARGET_PROPERTY)]
class SameAs implements Rule
{
    public function __construct(
        public string $property,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        return $value === ($dto->{$this->property} ?? null);
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value should match {field}.';
    }

    public function params(): array
    {
        return ['field' => $this->property];
    }
}
