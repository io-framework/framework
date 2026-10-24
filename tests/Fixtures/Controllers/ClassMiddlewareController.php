<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Middleware};
use Io\Tests\Fixtures\Middlewares\{AddHeaderMiddleware, ClassLevelMiddleware};
use Io\Tests\Fixtures\Support\CallTracker;

#[Controller('/class-middleware')]
#[Middleware(ClassLevelMiddleware::class)]
class ClassMiddlewareController
{
    #[Get('/plain')]
    public function plain(): array
    {
        CallTracker::record('controller');
        return ['ok' => true];
    }

    #[Get('/stacked')]
    #[Middleware(AddHeaderMiddleware::class)]
    public function stacked(): array
    {
        CallTracker::record('controller');
        return ['ok' => true];
    }
}
