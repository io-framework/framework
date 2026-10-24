<?php

namespace Io\Tests\Fixtures\Middlewares;

use Io\Http\{MiddlewareBase, Request, Response};
use Io\Tests\Fixtures\Support\CallTracker;

class ClassLevelMiddleware extends MiddlewareBase
{
    protected function before(Request $req, Response $res): bool
    {
        CallTracker::record('class-level');
        return true;
    }
}
