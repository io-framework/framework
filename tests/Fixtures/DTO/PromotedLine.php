<?php

namespace Io\Tests\Fixtures\DTO;

use Io\Attributes\{Min, Trim};

/** A readonly DTO with constructor promotion */
final readonly class PromotedLine
{
    public function __construct(
        #[Trim]
        public string $product,
        #[Min(1)]
        public int $quantity = 1,
        public ?string $note = null,
    ) {
    }
}
