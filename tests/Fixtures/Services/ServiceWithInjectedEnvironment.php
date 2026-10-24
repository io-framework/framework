<?php

namespace Io\Tests\Fixtures\Services;

use Io\Attributes\Inject;
use Io\Environment;

/**
 * A container-managed service that #[Inject]s Environment. Proves
 * Environment is genuinely usable via #[Inject]/container resolution, not
 * just via `new Environment()` directly.
 */
class ServiceWithInjectedEnvironment
{
    #[Inject]
    private Environment $environment;

    public function getEnvironment(): ?Environment
    {
        return isset($this->environment) ? $this->environment : null;
    }
}
