<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\Middleware;
use Io\Tests\Fixtures\Middlewares\AddHeaderMiddleware;

#[Middleware(AddHeaderMiddleware::class)]
abstract class InheritedGuardsBaseController extends InheritedGuardsRootController
{
}
