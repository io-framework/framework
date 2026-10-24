<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\Get;

/** Routes CacheChildController gets through its base class: a change here must invalidate the compiled cache. */
trait CacheRoutes
{
    #[Get('/from-trait')]
    public function fromTrait(): array
    {
        return [];
    }
}
