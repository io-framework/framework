<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get};
use Io\Http\Response;

#[Controller('/security')]
class SecurityHeaderController
{
    #[Get('/plain')]
    public function plain(): string
    {
        return 'ok';
    }

    // Sets X-Frame-Options itself, before SecurityHeadersMiddleware's
    // after() hook runs. Proves the middleware fills in only what's
    // missing rather than overwriting an explicit controller value.
    #[Get('/custom-frame-options')]
    public function customFrameOptions(Response $res): string
    {
        $res->withHeader('X-Frame-Options', 'SAMEORIGIN');
        return 'ok';
    }
}
