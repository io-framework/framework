<?php

namespace Io\Tests\Fixtures\DTO\Validation;

use Io\Attributes\{OneOf, SameAs};

class ChoiceDTO
{
    #[OneOf(['guest', 'user', 'admin'])]
    public $role;

    #[OneOf([1, 2])]
    public $level;

    public $password;

    #[SameAs('password')]
    public $passwordConfirm;
}
