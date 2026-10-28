<?php

namespace Io\Attributes;

use Attribute;

/**
 * Trims the text a request sends for a property before it is checked: `"  ada "` is `ada` for #[Length] and in
 * the DTO. On a class, every string property of it; `#[Trim(false)]` on a property keeps its spaces (a password).
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS | Attribute::TARGET_PARAMETER)]
class Trim
{
    public function __construct(public bool $trim = true)
    {
    }
}
