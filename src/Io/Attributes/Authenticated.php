<?php

namespace Io\Attributes;

use Attribute;

/**
 * Requires a signed-in principal (a JWT, see useJwt(), or a session
 * middleware's `$req->principal`) for this route, on the
 * class (every route on it, also those of subclasses) or a single method.
 * The route's access check throws Io\Exceptions\UnauthorizedException
 * when $req->principal is null; App's default handler for that exception decides between
 * a redirect to JWTOptions::$loginRedirectUrl (for a page navigation) and
 * a plain 401 Problem Details response (see its own doc).
 *
 * #[Role]/#[Permissions] both imply this: checking a role or permission
 * without being authenticated first makes no sense, so either one alone
 * is enough to require a JWT too.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Authenticated
{
}
