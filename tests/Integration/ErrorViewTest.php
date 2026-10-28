<?php

namespace Io\Tests\Integration;

use Io\App;
use Io\Options\RendererOptions;
use Io\Tests\Fixtures\Controllers\GuardedController;

/** RendererOptions::$errorView: a browser navigation gets the error as a page, everyone else problem+json. */
final class ErrorViewTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = App::create();
        $app->configure(function (RendererOptions $opts) {
            $opts->directory = __DIR__ . '/../Fixtures/Views';
            $opts->errorView = 'ErrorPage';
        });
        $app->attach([GuardedController::class]);
    }

    public function testANavigationGetsTheErrorViewWithTheStatus(): void
    {
        $output = $this->dispatch('GET', '/guarded/missing', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame(404, $this->lastResponse?->status());
        $this->assertStringContainsString('error page 404 Not Found', $output);
        $this->assertStringContainsString('Widget not found', $output);
        $this->assertStringStartsWith('text/html', $this->sentHeaders()['content-type'] ?? '');
    }

    public function testAPathWithoutRouteGetsTheErrorViewToo(): void
    {
        $output = $this->dispatch('GET', '/nowhere', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame(404, $this->lastResponse?->status());
        $this->assertStringContainsString('error page 404', $output);
    }

    public function testAnUnexpectedExceptionShowsThePageWithoutItsMessage(): void
    {
        $output = $this->dispatch('GET', '/guarded/boom', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame(500, $this->lastResponse?->status());
        $this->assertStringContainsString('error page 500 Internal Server Error', $output);
        $this->assertStringNotContainsString('unexpected failure', $output);
    }

    public function testAFetchCallKeepsProblemJson(): void
    {
        $output = $this->dispatch('GET', '/guarded/missing', ['Sec-Fetch-Mode' => 'cors']);

        $this->assertSame(404, $this->lastResponse?->status());
        $this->assertSame('Not Found', json_decode($output, true)['title'] ?? null);
    }

    public function testABrokenErrorViewFallsBackToProblemJson(): void
    {
        App::create()->configure(function (RendererOptions $opts) {
            $opts->errorView = 'DoesNotExist';
        });

        $output = $this->dispatch('GET', '/guarded/missing', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame(404, $this->lastResponse?->status());
        $this->assertSame('Not Found', json_decode($output, true)['title'] ?? null);
    }
}
