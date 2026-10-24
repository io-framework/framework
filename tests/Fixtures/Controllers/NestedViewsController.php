<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Query};
use Io\Http\{Request, View};

#[Controller('/nested')]
class NestedViewsController
{
    /** A view living one directory down (Fixtures/Views/Admin/Users.php + Users.css). */
    #[Get('/admin-users')]
    public function adminUsers(Request $req): View
    {
        return new View('Admin/Users');
    }

    /** Builds the view name from request input: what View's constructor guards against. */
    #[Get('/by-name')]
    public function byName(#[Query] string $name): View
    {
        return new View($name, null, partial: true);
    }
}
