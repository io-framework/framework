<?php

namespace Io\Tests\Fixtures\DTO;

use Io\FromArray;

/** A property without a default: required, since nothing else would set it */
class RequiredNameDTO
{
    use FromArray;

    public string $name;
    public int $qty = 1;
}
