<?php

namespace Io\Attributes;

use Attribute;

/**
 * Binds the request body to a method parameter: a DTO class (built by the
 * Hydrator, then validated), for an `array` parameter with `of: OrderLine::class`
 * a list of them, one per element, and for an `Io\Form` parameter with
 * `of: LoginForm::class` the DTO with its errors, which the method reads itself.
 * `validate: false` hands an invalid DTO to the method instead of answering 422;
 * a View it returns for an invalid DTO or Form still answers 422.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class Body
{
    /**
     * @param string|null $name Unused for bodies; kept for symmetry with #[Param]/#[Query].
     * @param class-string|null $of Element DTO class for an `array`-typed parameter, the DTO class of a `Form` one.
     * @param bool $validate False: no 422 before the method runs; it reads `$dto->isValid` and `$dto->errors` itself.
     */
    public function __construct(public ?string $name = null, public ?string $of = null, public bool $validate = true)
    {
    }
}
