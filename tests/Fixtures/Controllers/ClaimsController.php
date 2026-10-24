<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Claims, Controller, Get};
use Io\Tests\Fixtures\DTO\UserClaims;

#[Controller('/claims')]
class ClaimsController
{
    #[Get('/me')]
    public function me(#[Claims] UserClaims $user): array
    {
        return ['sub' => $user->sub, 'role' => $user->role];
    }

    #[Get('/maybe')]
    public function maybe(#[Claims] ?UserClaims $user): array
    {
        return ['sub' => $user?->sub];
    }
}
