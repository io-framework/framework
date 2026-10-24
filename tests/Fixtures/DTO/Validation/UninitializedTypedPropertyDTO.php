<?php

namespace Io\Tests\Fixtures\DTO\Validation;

use Io\Attributes\NotBlank;

class UninitializedTypedPropertyDTO
{
    // Typed, no default: PHP leaves this genuinely uninitialized until
    // assigned, unlike untyped/nullable properties which default to null.
    #[NotBlank]
    public string $name;
}
