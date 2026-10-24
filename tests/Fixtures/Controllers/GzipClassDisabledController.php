<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, NoGzip};

/** Class-level #[NoGzip]: every route on this controller must opt out. */
#[Controller('/gzip-class-disabled')]
#[NoGzip]
class GzipClassDisabledController
{
    #[Get('/big')]
    public function big(): string
    {
        return str_repeat('a', 2000);
    }
}
