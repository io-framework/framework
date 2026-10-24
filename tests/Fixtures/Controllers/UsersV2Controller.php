<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Version};

#[Controller('/users')]
#[Version('v2')]
class UsersV2Controller
{
    #[Get]
    public function list(): array
    {
        return ['from' => 'v2'];
    }
}
