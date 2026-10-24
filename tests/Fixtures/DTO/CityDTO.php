<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\NotBlank;
use Io\FromArray;

class CityDTO
{
    use FromArray;

    #[NotBlank]
    public string $city = '';
}
