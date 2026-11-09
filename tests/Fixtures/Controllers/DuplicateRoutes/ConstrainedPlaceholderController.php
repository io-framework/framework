<?php

namespace Io\Tests\Fixtures\Controllers\DuplicateRoutes;

use Io\Attributes\{Controller, Get};

/** /dup/{n:\d+}: only digits, so it matches other requests than /dup/{id} */
#[Controller('/dup')]
final class ConstrainedPlaceholderController
{
    #[Get('/{n:\d+}')]
    public function four(int $n): string { return 'four'; }
}
