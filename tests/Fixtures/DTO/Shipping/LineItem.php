<?php

namespace Io\Tests\Fixtures\DTO\Shipping;

use Io\FromArray;

class LineItem
{
    use FromArray;

    public int $parcels = 1;
}
