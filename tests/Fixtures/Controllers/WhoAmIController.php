<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Post, SkipCsrf};
use Io\Http\Request;

#[Controller('/whoami')]
class WhoAmIController
{
    #[Get]
    public function show(Request $req): array
    {
        return ['jwt' => $req->jwt, 'id' => $req->id];
    }

    /** A token-auth API route: no CSRF check, so no identity from the cookie */
    #[Post('/api')]
    #[SkipCsrf]
    public function api(Request $req): array
    {
        return ['jwt' => $req->jwt];
    }
}
