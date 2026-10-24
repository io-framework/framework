<?php

namespace Io\Tests\Fixtures\Middlewares;

use Io\Http\{MiddlewareBase, Request, Response};
use Io\Tests\Fixtures\Support\CallTracker;

class AddHeaderMiddleware extends MiddlewareBase
{
    protected function before(Request $req, Response $res): bool
    {
        CallTracker::record('add-header');
        $res->withHeader('X-Traced', 'yes');
        return true;
    }
}
