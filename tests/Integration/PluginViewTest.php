<?php

namespace Io\Tests\Integration;

use Io\Options\RendererOptions;
use Io\Tests\Fixtures\Controllers\PluginViewController;
use Io\App;

/** A view from a directory of its own (a plugin's) shares its name with an app view that has CSS/JS. */
final class PluginViewTest extends IntegrationTestCase
{
    public function testItRendersFromItsDirectoryWithoutTheAppViewsAssets(): void
    {
        $app = App::create();
        $app->configure(function (RendererOptions $opts) {
            $opts->directory = __DIR__ . '/../Fixtures/Views';
        });
        $app->attach([PluginViewController::class]);

        $output = $this->dispatch('GET', '/plugin-view');

        $this->assertSame("<main><p>plugin home</p>\n</main>\n", $output);
    }
}
