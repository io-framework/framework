<?php

namespace Io\Tests\Integration;

use Io\{App, JWT};
use Io\Options\{JWTOptions, PermissionOptions};
use Io\Tests\Fixtures\Controllers\{AuthorizationController, ClassAuthenticatedController, ClassRoleController, InheritedGuardsController};
use Io\Tests\Fixtures\Support\FakePermissionProvider;

final class AuthorizationTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = App::create();
        $app->configure(function (JWTOptions $opts) {
            $opts->secret = 'test-secret-long-enough-for-hmac-32';
        });
        $app->attach([AuthorizationController::class, ClassAuthenticatedController::class, ClassRoleController::class, InheritedGuardsController::class]);
        $app->useJwt();
    }

    public function testARouteWithNoAttributeNeedsNoJwtAtAll(): void
    {
        $output = $this->dispatch('GET', '/authz/public');

        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));
    }

    public function testAuthenticatedRouteRejectsAMissingJwtWith401(): void
    {
        $output = $this->dispatch('GET', '/authz/authenticated');

        $this->assertSame(401, $this->lastResponse?->status());
        $this->assertSame('Unauthorized', json_decode($output, true)['title'] ?? null);
    }

    public function testAuthenticatedRouteAllowsAValidJwt(): void
    {
        $token = JWT::encode(['sub' => '1']);

        $output = $this->dispatch('GET', '/authz/authenticated', ['Authorization' => "Bearer $token"]);

        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));
    }

    public function testClassLevelAuthenticatedAppliesToAMethodWithNoAttributeOfItsOwn(): void
    {
        $output = $this->dispatch('GET', '/authz-class/plain');

        $this->assertSame(401, $this->lastResponse?->status());
        $this->assertSame('Unauthorized', json_decode($output, true)['title'] ?? null);
    }

    public function testGuardsAndMiddlewaresOfBaseControllersApplyToTheirSubclasses(): void
    {
        $output = $this->dispatch('GET', '/inherited/secret');
        $this->assertSame(401, $this->lastResponse?->status(), 'an #[Authenticated] on a base controller left the route open');
        $this->assertSame('Unauthorized', json_decode($output, true)['title'] ?? null);

        $token = JWT::encode(['sub' => '1']);
        $output = $this->dispatch('GET', '/inherited/secret', ['Authorization' => "Bearer $token"]);
        $this->assertSame(['secret' => true], json_decode($output, true));
        $this->assertSame('yes', $this->sentHeaders()['x-traced'] ?? null);
    }

    public function testRoleRouteImpliesAuthenticationAndRejectsAMissingJwtWith401(): void
    {
        $this->dispatch('GET', '/authz/admin-role');

        $this->assertSame(401, $this->lastResponse?->status());
    }

    public function testRoleRouteRejectsAMismatchedRoleWith403(): void
    {
        $token = JWT::encode(['sub' => '1', 'role' => 'user']);

        $output = $this->dispatch('GET', '/authz/admin-role', ['Authorization' => "Bearer $token"]);

        $this->assertSame(403, $this->lastResponse?->status());
        $this->assertSame('Forbidden', json_decode($output, true)['title'] ?? null);
    }

    public function testRoleRouteAllowsAMatchingRole(): void
    {
        $token = JWT::encode(['sub' => '1', 'role' => 'admin']);

        $output = $this->dispatch('GET', '/authz/admin-role', ['Authorization' => "Bearer $token"]);

        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));
    }

    public function testMethodLevelRoleOverridesTheClassLevelRole(): void
    {
        $editorToken = JWT::encode(['sub' => '1', 'role' => 'editor']);
        $adminToken = JWT::encode(['sub' => '1', 'role' => 'admin']);

        // Class says 'editor'; the method overrides it to 'admin', so an
        // editor token must now be rejected on this specific route, even
        // though it satisfies every *other* route on the same class.
        $this->dispatch('GET', '/authz-role/override', ['Authorization' => "Bearer $editorToken"]);
        $this->assertSame(403, $this->lastResponse?->status());

        $output = $this->dispatch('GET', '/authz-role/override', ['Authorization' => "Bearer $adminToken"]);
        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));

        $output = $this->dispatch('GET', '/authz-role/plain', ['Authorization' => "Bearer $editorToken"]);
        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));
    }

    public function testPermissionsRouteFailsClosedWithNoProviderConfigured(): void
    {
        $token = JWT::encode(['sub' => '1']);

        $output = $this->dispatch('GET', '/authz/manage-users', ['Authorization' => "Bearer $token"]);

        $this->assertSame(403, $this->lastResponse?->status());
        $this->assertSame('Forbidden', json_decode($output, true)['title'] ?? null);
    }

    public function testPermissionsRouteAllowsAGrantedPermission(): void
    {
        $provider = new FakePermissionProvider();
        $provider->granted = ['users.manage'];
        App::create()->configure(function (PermissionOptions $opts) use ($provider) {
            $opts->provider = $provider;
        });
        $token = JWT::encode(['sub' => '1']);

        $output = $this->dispatch('GET', '/authz/manage-users', ['Authorization' => "Bearer $token"]);

        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));
    }

    public function testAProviderAPluginBoundCountsWhenTheAppSetNone(): void
    {
        $provider = new FakePermissionProvider();
        $provider->granted = ['users.manage'];
        // What a plugin does in its boot()
        App::create()->getContainer()->bind(\Io\PermissionProvider::class, $provider);
        $token = JWT::encode(['sub' => '1']);

        $this->dispatch('GET', '/authz/manage-users', ['Authorization' => "Bearer $token"]);
        $this->assertSame(200, $this->lastResponse?->status());
        $this->dispatch('GET', '/authz/multi-permission', ['Authorization' => "Bearer $token"]);
        $this->assertSame(403, $this->lastResponse?->status());
    }

    public function testPermissionsRouteRejectsAnUngrantedPermission(): void
    {
        $provider = new FakePermissionProvider();
        $provider->granted = ['something.else'];
        App::create()->configure(function (PermissionOptions $opts) use ($provider) {
            $opts->provider = $provider;
        });
        $token = JWT::encode(['sub' => '1']);

        $output = $this->dispatch('GET', '/authz/manage-users', ['Authorization' => "Bearer $token"]);

        $this->assertSame(403, $this->lastResponse?->status());
    }

    public function testUnauthenticatedRequestStaysPlainJsonWithNoLoginRedirectUrlConfigured(): void
    {
        // Default JWTOptions::$loginRedirectUrl is null: a real browser
        // navigation must still get plain 401 Problem Details, not a
        // surprise redirect nothing asked for.
        $output = $this->dispatch('GET', '/authz/authenticated', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame(401, $this->lastResponse?->status());
        $this->assertSame('Unauthorized', json_decode($output, true)['title'] ?? null);
    }

    public function testUnauthenticatedBrowserNavigationRedirectsWhenLoginRedirectUrlIsConfigured(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->loginRedirectUrl = '/login';
        });

        $this->dispatch('GET', '/authz/authenticated', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame(302, $this->lastResponse?->status());
        $this->assertSame('/login?next=%2Fauthz%2Fauthenticated', $this->sentHeaders()['location'] ?? null);
    }

    public function testTheLoginRedirectCarriesTheQueryAndKeepsTheLoginUrlsOwn(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->loginRedirectUrl = '/login?lang=de';
        });

        $this->dispatch('GET', '/authz/authenticated?page=2', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame('/login?lang=de&next=' . rawurlencode('/authz/authenticated?page=2'), $this->sentHeaders()['location'] ?? null);
    }

    public function testTheLoginRedirectLeavesTheReturnOutWithoutAParameter(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->loginRedirectUrl = '/login';
            $opts->returnParameter = null;
        });

        $this->dispatch('GET', '/authz/authenticated', ['Sec-Fetch-Mode' => 'navigate']);

        $this->assertSame('/login', $this->sentHeaders()['location'] ?? null);
    }

    public function testUnauthenticatedFetchCallStillGetsPlainJsonEvenWithLoginRedirectUrlConfigured(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->loginRedirectUrl = '/login';
        });

        // Sec-Fetch-Mode: same-origin is what an ordinary same-origin
        // fetch()/XHR call sends (never 'navigate'), so this must fall
        // through to the plain 401 below regardless of the option.
        $output = $this->dispatch('GET', '/authz/authenticated', ['Sec-Fetch-Mode' => 'same-origin']);

        $this->assertSame(401, $this->lastResponse?->status());
        $this->assertSame('Unauthorized', json_decode($output, true)['title'] ?? null);
    }

    public function testUnauthenticatedRequestWithNoSecFetchModeHeaderStaysPlainJson(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->loginRedirectUrl = '/login';
        });

        // No Sec-Fetch-Mode at all (an older browser, or a non-browser
        // client like curl) is treated as "not a navigation", the safe
        // default.
        $output = $this->dispatch('GET', '/authz/authenticated');

        $this->assertSame(401, $this->lastResponse?->status());
        $this->assertSame('Unauthorized', json_decode($output, true)['title'] ?? null);
    }

    public function testMultiplePermissionsAllMustBeGranted(): void
    {
        $provider = new FakePermissionProvider();
        $provider->granted = ['users.manage']; // missing 'users.delete'
        App::create()->configure(function (PermissionOptions $opts) use ($provider) {
            $opts->provider = $provider;
        });
        $token = JWT::encode(['sub' => '1']);

        $this->dispatch('GET', '/authz/multi-permission', ['Authorization' => "Bearer $token"]);
        $this->assertSame(403, $this->lastResponse?->status());

        $provider->granted = ['users.manage', 'users.delete'];
        $output = $this->dispatch('GET', '/authz/multi-permission', ['Authorization' => "Bearer $token"]);
        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertSame(['ok' => true], json_decode($output, true));
    }

}
