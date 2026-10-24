<?php

namespace Io\Tests\Fixtures\Middlewares;

use Io\Http\{MiddlewareBase, Request, Response};

final class VetoingMiddleware extends MiddlewareBase
{
    protected function before(Request $req, Response $res): bool
    {
        return false;
    }
}
