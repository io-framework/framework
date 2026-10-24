<?php

namespace Io\Tests\Fixtures\Controllers\DuplicateRoutes;

use Io\Attributes\{Controller, Get};

#[Controller('/dup')]
final class DynamicDuplicateController
{
    #[Get('/{id}')]
    public function one(int $id): string { return 'one'; }
}
