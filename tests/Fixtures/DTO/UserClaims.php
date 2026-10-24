<?php

namespace Io\Tests\Fixtures\DTO;

use Io\FromArray;

/** What the app's JWTs carry */
class UserClaims
{
    use FromArray;

    public int $sub;
    public string $role = 'user';
}
