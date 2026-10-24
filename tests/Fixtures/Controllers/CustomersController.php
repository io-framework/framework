<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Http\{Request, View};
use Io\Attributes\{Controller, Get, Summary, Tags, Param, Query};

#[Controller('/customers')]
class CustomersController
{
    #[Get]
    public function index(Request $request): View
    {
        return new View('HomePage');
    }

    #[Get('/{customerId}/orders')]
    #[Summary('List customer orders')]
    #[Tags(['orders', 'customers'])]
    public function getOrdersByCustomerId(
        #[Param] string $customerId,
        #[Query] ?string $status = null
    ): array {
        return ['customerId' => $customerId, 'status' => $status];
    }
}