<?php

namespace Io\Http;

use InvalidArgumentException;

/**
 * Returned from a route method to send a redirect instead of a body:
 * `return new Redirect('/backoffice')` next to the View the same handler
 * renders on failure (POST/redirect/GET). Router turns it into status +
 * Location; cookies/headers already queued on the Response are kept.
 * A plain result, not an exception: a redirect is a normal outcome, and
 * `View|Redirect` on the signature says so. Auth failures that should
 * redirect to the login page go through UnauthorizedException + JWTOptions::$loginRedirectUrl, which hands
 * the page over as ?next=; Redirect::back() takes the visitor there after the login.
 */
final class Redirect
{
    /**
     * @param string $location Sent verbatim as the Location header.
     * @param int $status Any 3xx; 302 by default (303 for an explicit See Other, 301/308 permanent, 307 keeps the method).
     */
    public function __construct(public readonly string $location, public readonly int $status = 302)
    {
        if ($status < 300 || $status > 399) {
            throw new InvalidArgumentException("Redirect status must be a 3xx code, got {$status}.");
        }
        if ($location === '') {
            throw new InvalidArgumentException('Redirect location must not be empty.');
        }
        // A line break would let a request-derived location inject a second header.
        if (str_contains($location, "\r") || str_contains($location, "\n")) {
            throw new InvalidArgumentException('Redirect location must not contain line breaks.');
        }
    }

    /**
     * Back to where the visitor came from: the path in the query's or the form's $parameter (`?next=`, a hidden
     * field), else $default. Only a path of this site is followed, so a link to the login page cannot send
     * someone elsewhere afterwards.
     */
    public static function back(Request $req, string $default = '/', string $parameter = 'next', int $status = 303): self
    {
        $next = $req->query($parameter) ?? $req->body($parameter);

        return new self(is_string($next) && self::isLocal($next) ? $next : $default, $status);
    }

    /**
     * A path of this site: one leading slash, then no second one and no backslash (browsers read both as
     * "another host"), no whitespace or control characters.
     */
    public static function isLocal(string $url): bool
    {
        return preg_match('#^/(?![/\\\\])[^\\\\\s\x00-\x1f\x7f]*$#', $url) === 1;
    }
}
