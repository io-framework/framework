<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\{Email, Length, NotBlank, Trim};

/** A plain DTO: no trait, no constructor; names trimmed, the password not */
#[Trim]
final class PlainSignup
{
    #[NotBlank]
    #[Email]
    public string $email = '';

    #[Length(min: 3, max: 12)]
    public string $username = '';

    #[Trim(false)]
    #[Length(min: 8)]
    public string $password = '';

    public int $age = 0;

    public ?PlainAddress $address = null;
}
