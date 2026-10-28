<?php

namespace Io\Tests\Integration;

use Io\App;
use Io\OpenAPI\OpenAPIGenerator;
use Io\Tests\Fixtures\Controllers\PatternsController;

/** {name:pattern} placeholders: matched, typed, a 404 when the path does not fit, plain in the API spec */
final class RoutePatternTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        App::create()->attach([PatternsController::class]);
    }

    public function testAPathThatFitsTheirPatternsBindsThePlaceholders(): void
    {
        $this->assertSame(['id' => 26, 'slug' => 'omae-gotoki'], json_decode($this->dispatch('GET', '/patterns/a/26-omae-gotoki'), true));
        $this->assertSame(['id' => 7, 'slug' => ''], json_decode($this->dispatch('GET', '/patterns/a/7-'), true));
        $this->assertSame(['year' => 2026], json_decode($this->dispatch('GET', '/patterns/year/2026'), true));
        $this->assertSame(['name' => 'x'], json_decode($this->dispatch('GET', '/patterns/plain/x'), true));
    }

    public function testAPathThatDoesNotFitIsNotFound(): void
    {
        foreach (['/patterns/a/x-omae', '/patterns/a/26-Omae', '/patterns/year/26', '/patterns/year/20260'] as $path) {
            $this->dispatch('GET', $path);
            $this->assertSame(404, $this->lastResponse?->status(), $path);
        }
    }

    public function testTheApiSpecNamesThePlaceholdersWithoutTheirPatterns(): void
    {
        $paths = (new OpenAPIGenerator(App::create()->getRouter()))->generate()['paths'] ?? [];

        $this->assertArrayHasKey('/patterns/a/{id}-{slug}', $paths);
        $this->assertArrayHasKey('/patterns/year/{year}', $paths);
        $this->assertSame(['id', 'slug'], array_column($paths['/patterns/a/{id}-{slug}']['get']['parameters'], 'name'));
    }
}
