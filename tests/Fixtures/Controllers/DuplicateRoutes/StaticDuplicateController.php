<?php

namespace Io\Tests\Fixtures\Controllers\DuplicateRoutes;

use Io\Attributes\{Controller, Get};

#[Controller('/dup')]
final class StaticDuplicateController
{
    #[Get('/x')]
    public function one(): string { return 'one'; }

    #[Get('/x')]
    public function two(): string { return 'two'; }
}
