<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Inject};
use Io\Tests\Fixtures\Services\{AnotherService, ServiceWithInjectedDependency};

#[Controller('/nested-injection')]
class NestedInjectionController
{
    #[Inject]
    private ServiceWithInjectedDependency $service;

    #[Get]
    public function check(): array
    {
        return [
            'serviceResolved' => isset($this->service),
            'nestedDependencyResolved' => $this->service->getOther() instanceof AnotherService,
        ];
    }
}
