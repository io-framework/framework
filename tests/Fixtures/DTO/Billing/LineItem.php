<?php

namespace Io\Tests\Fixtures\DTO\Billing;

use Io\FromArray;

class LineItem
{
    use FromArray;

    public string $sku = '';
}
