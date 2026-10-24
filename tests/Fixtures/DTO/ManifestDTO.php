<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\ListOf;

/** A response DTO whose array properties are promoted: #[ListOf] on the constructor parameter applies to the property */
final class ManifestDTO
{
    public function __construct(
        #[ListOf(CityDTO::class)]
        public array $stops = [],
        #[ListOf(
            type: 'string',
            map: true
        )]
        public array $labels = [],
    ) {
    }
}
