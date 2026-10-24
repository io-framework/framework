<?php

namespace Io\Tests\Fixtures\Services;

use Io\Attributes\Inject;
use Io\Logger;

/**
 * A container-managed service that #[Inject]s Logger. Proves Logger is
 * genuinely usable via #[Inject]/container resolution, not just via `new
 * Logger()` directly.
 */
class ServiceWithInjectedLogger
{
    #[Inject]
    private Logger $logger;

    public function getLogger(): ?Logger
    {
        return isset($this->logger) ? $this->logger : null;
    }
}
