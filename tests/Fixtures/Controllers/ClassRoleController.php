<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Role};

#[Controller('/authz-role')]
#[Role('editor')]
class ClassRoleController
{
    #[Get('/plain')]
    public function plain(): array
    {
        return ['ok' => true];
    }

    // Method-level #[Role] overrides the class-level one (see
    // RouteCompiler::resolveOverridable()'s "method wins" precedence).
    #[Get('/override')]
    #[Role('admin')]
    public function override(): array
    {
        return ['ok' => true];
    }
}
