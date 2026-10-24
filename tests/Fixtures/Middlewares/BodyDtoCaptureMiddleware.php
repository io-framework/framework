<?php

namespace Io\Tests\Fixtures\Middlewares;

use Io\Http\{MiddlewareBase, Request, Response};
use Io\Tests\Fixtures\Support\CallTracker;

/**
 * Proves Request::$bodyDto is populated by the time an after() hook runs:
 * Router::buildMethodArguments()'s 'Body' case sets it before the
 * controller (which after() wraps) is even called, see its own doc.
 */
class BodyDtoCaptureMiddleware extends MiddlewareBase
{
    protected function after(Request $req, Response $res): void
    {
        CallTracker::record('bodyDto:' . ($req->bodyDto !== null ? get_class($req->bodyDto) : 'null'));
    }
}
