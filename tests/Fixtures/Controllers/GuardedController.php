<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get};
use Io\Exceptions\{ForbiddenException, UnauthorizedException, NotFoundException};
use Io\Tests\Fixtures\Support\{SpecificForbiddenException, CustomNotFoundInterfaceException};
use RuntimeException;

#[Controller('/guarded')]
class GuardedController
{
    #[Get('/forbidden')]
    public function forbidden(): never
    {
        throw new ForbiddenException();
    }

    #[Get('/forbidden-subclass')]
    public function forbiddenSubclass(): never
    {
        throw new SpecificForbiddenException();
    }

    #[Get('/not-found-interface')]
    public function notFoundInterface(): never
    {
        throw new CustomNotFoundInterfaceException('via interface');
    }

    #[Get('/unauthorized')]
    public function unauthorized(): never
    {
        throw new UnauthorizedException();
    }

    #[Get('/missing')]
    public function missing(): never
    {
        throw new NotFoundException('Widget not found');
    }

    #[Get('/boom')]
    public function boom(): never
    {
        throw new RuntimeException('unexpected failure');
    }
}
