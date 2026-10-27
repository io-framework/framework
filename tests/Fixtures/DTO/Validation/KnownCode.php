<?php

namespace Io\Tests\Fixtures\DTO\Validation;

use Io\Validation\Checker;

/** A Checker as an app writes one: here a fixed list instead of the database. */
class KnownCode implements Checker
{
    public function passes(mixed $value, object $dto): bool
    {
        return $value === null || in_array($value, ['ABC', 'XYZ'], true);
    }
}
