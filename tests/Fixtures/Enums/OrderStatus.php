<?php

namespace Io\Tests\Fixtures\Enums;

enum OrderStatus: string
{
    case Open = 'open';
    case Done = 'done';
}
