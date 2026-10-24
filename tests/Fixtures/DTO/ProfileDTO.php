<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\Ignore;
use Io\FromArray;

/** #[Ignore] properties: never in a result, a schema or set from input */
class ProfileDTO
{
    use FromArray;

    public string $name = '';

    #[Ignore]
    public string $passwordHash = '';

    #[Ignore]
    public int $internalId;

    public ?ProfileDTO $friend = null;
}
