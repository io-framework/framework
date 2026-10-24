<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Authenticated, Middleware};
use Io\Tests\Fixtures\Middlewares\AddHeaderMiddleware;

/** Its attributes reach CacheChildController: a change here must invalidate the compiled cache. */
#[Authenticated]
#[Middleware(AddHeaderMiddleware::class)]
abstract class CacheBaseController
{
    use CacheRoutes;
}
