<?php

namespace Io\Tests\Fixtures\Middlewares;

use Io\Attributes\Inject;
use Io\Http\{MiddlewareBase, Request, Response};
use Io\Tests\Fixtures\Services\ExampleService;
use Io\Tests\Fixtures\Support\CallTracker;

class InjectingMiddleware extends MiddlewareBase
{
    #[Inject]
    private ExampleService $service;

    protected function before(Request $req, Response $res): bool
    {
        CallTracker::record('injecting:' . (isset($this->service) ? get_class($this->service) : 'MISSING'));
        return true;
    }
}
