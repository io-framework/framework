<?php

namespace Io\Tests\Fixtures\Plugins;

use Io\{Container, Environment};
use Io\Options\CorsOptions;
use Io\Plugin\PluginBase;

/** A boot() whose parameters ask for the container, an Options class and a service. */
final class InjectedBootPlugin extends PluginBase
{
    /** @var list<object> */
    public array $got = [];

    public function name(): string
    {
        return 'injected-boot';
    }

    public function boot(Container $container, CorsOptions $cors, Environment $env): void
    {
        $this->got = [$container, $cors, $env];
    }
}
