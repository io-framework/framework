<?php

namespace Io\Tests\Integration;

use Io\App;
use Io\OpenAPI\OpenAPIGenerator;
use Io\Options\JWTOptions;
use Io\Tests\Fixtures\Controllers\ClaimsController;

final class ClaimsTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = App::create();
        $app->configure(fn (JWTOptions $jwt) => $jwt->secret = 'test-secret-long-enough-for-hmac-32');
        $app->attach([ClaimsController::class]);
        $app->useJwt();
    }

    public function testTheClaimsArriveAsTheParametersDto(): void
    {
        $this->request('GET', '/claims/me')->withJwt(['sub' => 7, 'role' => 'admin'])->send()->assertOk()->assertJson(['sub' => 7, 'role' => 'admin']);
    }

    public function testWithoutATokenOrWithClaimsThatDontFitTheRouteAnswers401(): void
    {
        $this->request('GET', '/claims/me')->send()->assertStatus(401);
        $this->request('GET', '/claims/me')->withJwt(['role' => 'admin'])->send()->assertStatus(401);
    }

    public function testANullableClaimsParameterIsOptional(): void
    {
        $this->request('GET', '/claims/maybe')->send()->assertOk()->assertJson(['sub' => null]);
        $this->request('GET', '/claims/maybe')->withJwt(['sub' => 3])->send()->assertOk()->assertJson(['sub' => 3]);
    }

    public function testOpenApiDocumentsTheRouteAsGuarded(): void
    {
        $paths = (new OpenAPIGenerator(App::create()->getRouter()))->generate()['paths'];

        $this->assertSame([['bearerAuth' => []]], $paths['/claims/me']['get']['security']);
        $this->assertArrayNotHasKey('security', $paths['/claims/maybe']['get']);
        $this->assertSame([], $paths['/claims/me']['get']['parameters'] ?? []);
    }
}
