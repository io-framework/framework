<?php

namespace Io\Tests\Fixtures\Middlewares;

use Io\Http\MiddlewareBase;

final class PlainMiddleware extends MiddlewareBase
{
    // No before()/after() overrides: exercises MiddlewareBase's own
    // default pass-through implementations directly.
}
