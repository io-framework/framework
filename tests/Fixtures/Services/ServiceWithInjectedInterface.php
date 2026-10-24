<?php

namespace Io\Tests\Fixtures\Services;

use Io\Attributes\Inject;
use Io\PermissionProvider;

class ServiceWithInjectedInterface
{
    #[Inject]
    public PermissionProvider $permissions;
}
