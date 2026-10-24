<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Authenticated};

#[Controller('/authz-class')]
#[Authenticated]
class ClassAuthenticatedController
{
    #[Get('/plain')]
    public function plain(): array
    {
        return ['ok' => true];
    }
}
