<?php

namespace Io\Tests\Fixtures\Controllers\DuplicateRoutes;

use Io\Attributes\{Controller, Get, Post};

#[Controller('/dup')]
final class DynamicDuplicateAgainController
{
    #[Get('/{id}')]
    public function two(int $id): string { return 'two'; }

    #[Post('/{id}')]
    public function post(int $id): string { return 'post'; }
}
