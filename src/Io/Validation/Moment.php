<?php

namespace Io\Validation;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

/**
 * The point in time #[Before] and #[After] compare.
 * @internal
 */
final class Moment
{
    /** A DateTimeInterface as it is, a string as DateTimeImmutable reads it; null for anything else. */
    public static function of(mixed $value): ?DateTimeImmutable
    {
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }
        if (!is_string($value) || trim($value) === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }
    }
}
