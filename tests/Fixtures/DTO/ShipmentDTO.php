<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\ListOf;
use Io\FromArray;
use Io\Tests\Fixtures\Enums\{OrderStatus, Priority};

/** Enums, a nested DTO and lists of both, for what FromArray builds beyond scalars */
class ShipmentDTO
{
    use FromArray;

    public OrderStatus $status = OrderStatus::Open;
    public ?Priority $priority = null;
    public CityDTO $to;
    public ?CityDTO $via = null;

    #[ListOf(CityDTO::class)]
    public array $stops = [];

    #[ListOf(Priority::class)]
    public array $levels = [];

    #[ListOf(
        type: 'int',
        map: true
    )]
    public array $weights = [];

    public private(set) int $id = 0;
}
