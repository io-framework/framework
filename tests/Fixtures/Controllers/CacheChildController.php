<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get};

#[Controller('/cache-child')]
class CacheChildController extends CacheBaseController
{
    #[Get('/')]
    public function index(): array
    {
        return [];
    }
}
