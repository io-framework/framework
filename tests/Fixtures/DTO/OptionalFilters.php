<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\{Email, Length, Regex};
use Io\Tests\Fixtures\Enums\Priority;

/** A search form whose fields may all stay empty: browsers send '' for each of them */
final class OptionalFilters
{
    public ?int $minimum = null;

    public ?float $ratio = null;

    public ?Priority $priority = null;

    public int $page = 3;

    #[Email]
    public ?string $email = null;

    #[Length(min: 3)]
    #[Regex('/^[a-z]+$/')]
    public ?string $name = null;
}
