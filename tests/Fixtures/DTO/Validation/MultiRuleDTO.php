<?php

namespace Io\Tests\Fixtures\DTO\Validation;

use Io\Attributes\{NotBlank, Length};

class MultiRuleDTO
{
    #[NotBlank]
    #[Length(
        min: 3,
        max: 5
    )]
    public $code;
}
