<?php

namespace Io\Tests\Fixtures\Attributes;

use Io\Attributes\{Param, Query, Body};

class ParameterBindingFixture
{
    public function handle(
        #[Param] string $customerId,
        #[Param('uuid')] string $userId,
        #[Query] ?string $search,
        #[Query('q')] ?string $term,
        #[Body('payload')] array $body,
        #[Body(of: 'Some\\DTO')] array $items
    ) {
    }
}
