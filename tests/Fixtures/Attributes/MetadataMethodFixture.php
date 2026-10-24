<?php

namespace Io\Tests\Fixtures\Attributes;

use Io\Attributes\{Summary, Tags, Throws, Middleware};
use Io\Exceptions\NotFoundException;

class MetadataMethodFixture
{
    #[Summary('Does the thing')]
    #[Tags(['widgets', 'admin'])]
    #[Throws(
        exception: NotFoundException::class,
        status: 404,
        description: 'Widget missing'
    )]
    #[Middleware(MetadataAuthMiddleware::class)]
    #[Middleware(MetadataLogMiddleware::class)]
    public function handle() {}
}
