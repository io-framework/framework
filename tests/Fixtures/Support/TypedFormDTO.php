<?php

namespace Io\Tests\Fixtures\Support;

use Io\FromArray;

/** Scalars of every kind, for what FromArray converts and what it refuses */
class TypedFormDTO
{
    use FromArray;

    public bool $admin = false;
    public int $qty = 1;
    public float $price = 0.0;
    public string $name = '';
}
