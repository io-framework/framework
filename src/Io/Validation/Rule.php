<?php

namespace Io\Validation;

/**
 * A validation attribute on a DTO property. The Validator applies every attribute that implements it, in the
 * order they are written, and reports the message of each one that fails.
 */
interface Rule
{
    /** Whether $value passes; $dto is the whole object, for rules that compare with another property. */
    public function passes(mixed $value, object $dto): bool;

    /**
     * The message for a $value that failed, with its {placeholders}: the rule's own `message:` or its English
     * text, which is also the catalog key.
     */
    public function message(mixed $value): string;

    /** @return array<string, scalar> the values for the message's placeholders */
    public function params(): array;
}
