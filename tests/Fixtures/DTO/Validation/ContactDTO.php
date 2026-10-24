<?php

namespace Io\Tests\Fixtures\DTO\Validation;

use Io\Attributes\{NotBlank, Email};

class ContactDTO
{
    #[NotBlank]
    public $name;

    #[Email]
    public $email;
}
