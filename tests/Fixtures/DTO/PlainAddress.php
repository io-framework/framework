<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\NotBlank;

final class PlainAddress
{
    #[NotBlank]
    public string $city = '';
}
