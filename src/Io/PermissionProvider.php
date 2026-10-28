<?php

namespace Io;

/**
 * App-provided permission lookup for #[Permissions(...)], configured
 * via Io\Options\PermissionOptions::$provider (App::configure()),
 * the same "Io owns the mechanism, the app owns the data" split
 * LoggerOptions uses for PSR-3 loggers. Io has no ORM/database
 * layer of its own (deliberately; see the apps' own Repository
 * classes), so it has no way to know what a "permission" actually means
 * or where roles/permissions are stored; this interface is the entire
 * contract.
 */
interface PermissionProvider
{
    /**
     * True if $jwt (the current request's principal, i.e.
     * Request::$principal: a JWT payload or a session's claims, shape app-defined)
     * currently holds $permission.
     */
    public function hasPermission(mixed $jwt, string $permission): bool;
}
