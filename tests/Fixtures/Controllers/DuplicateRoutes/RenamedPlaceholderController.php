<?php

namespace Io\Tests\Fixtures\Controllers\DuplicateRoutes;

use Io\Attributes\{Controller, Get};

/** /dup/{slug}: the same requests as DynamicDuplicateController's /dup/{id}, under another name */
#[Controller('/dup')]
final class RenamedPlaceholderController
{
    #[Get('/{slug}')]
    public function three(string $slug): string { return 'three'; }
}
