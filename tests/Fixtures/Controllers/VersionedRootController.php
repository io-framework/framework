<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Version};

/** A versioned controller at the root: its index is the version's path itself, /v1. */
#[Controller('/')]
#[Version('v1')]
class VersionedRootController
{
    #[Get('/')]
    public function index(): array
    {
        return ['from' => 'root'];
    }
}
