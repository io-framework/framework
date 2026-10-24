<?php

namespace Io\Tests\Fixtures\PHPStan;

use Io\Attributes\ListOf;
use Io\Attributes\Returns;

final class AttributeTyped
{
    #[ListOf('string')]
    public array $tags = [];

    public function __construct(
        #[ListOf(
            type: 'string',
            map: true
        )]
        public array $names,
    ) {
    }

    #[Returns(
        type: 'string',
        list: true
    )]
    public function typed(): array
    {
        return $this->tags;
    }

    public function untyped(): array
    {
        return $this->names;
    }
}
