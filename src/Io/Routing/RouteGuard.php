<?php

namespace Io\Routing;

use Io\PermissionProvider;

use Io\Container;
use Io\Csrf;
use Io\Enums\Message;
use Io\Exceptions\{ForbiddenException, UnauthorizedException};
use Io\Http\Request;
use Io\Options\PermissionOptions;

/**
 * The per-request access checks a compiled route carries: #[Authenticated]/
 * #[Role]/#[Permissions] (folded into 'authenticated'/'role'/'permissions'
 * by RouteCompiler) and CSRF verification for state-changing methods
 * unless #[SkipCsrf]. A plan predating a key defaults to the safe side:
 * no auth requirement, but CSRF checked.
 */
final class RouteGuard
{
    private const CSRF_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct(private ?Container $container)
    {
    }

    /** @param array<string, mixed> $route */
    public function check(array $route, string $httpMethod, Request $req): void
    {
        $unsafe = in_array(strtoupper($httpMethod), self::CSRF_METHODS, true);
        // Without the CSRF check a cookie proves nothing: any page could have made the browser send it
        if ($unsafe && ($route['skipCsrf'] ?? false) && $req->principalFromCookie) {
            $req->principal = null;
            $req->principalFromCookie = false;
        }
        if ($route['authenticated'] ?? false) {
            $this->checkAuthentication($route, $req);
        }

        if (
            $unsafe
            && !($route['skipCsrf'] ?? false)
            && $this->container !== null
            && !$this->container->get(Csrf::class)->verify($req)
        ) {
            throw new ForbiddenException(Message::CsrfTokenInvalid->value);
        }
    }

    /** @param array<string, mixed> $route */
    private function checkAuthentication(array $route, Request $req): void
    {
        $principal = $req->principal;
        if ($principal === null) {
            throw new UnauthorizedException();
        }

        $role = $route['role'] ?? null;
        if (is_string($role) && !self::hasRole($principal, $role)) {
            throw new ForbiddenException();
        }

        $permissions = $route['permissions'] ?? null;
        if (!is_array($permissions) || $permissions === []) {
            return;
        }
        // The app's own, else one a plugin bound with Container::bind(PermissionProvider::class, ...); none fails closed.
        $provider = $this->container?->get(PermissionOptions::class)->provider
            ?? ($this->container?->isRegistered(PermissionProvider::class) === true ? $this->container->get(PermissionProvider::class) : null);
        foreach ($permissions as $permission) {
            if (!is_string($permission) || $provider === null || !$provider->hasPermission($principal, $permission)) {
                throw new ForbiddenException();
            }
        }
    }

    /**
     * A 'role' claim equal to $role, or $role among the 'roles' claim's: a JWT carries one role, a session as many as
     * the user has.
     *
     * @param array<string, mixed> $principal
     */
    private static function hasRole(array $principal, string $role): bool
    {
        $roles = $principal['roles'] ?? null;

        return ($principal['role'] ?? null) === $role || (is_array($roles) && in_array($role, $roles, true));
    }
}
