---
title: "PermissionProvider"
description: "App-provided permission lookup for #[Permissions(...)], configured via Io\\Options\\PermissionOptions::$provider (App::configure()), the same \"Io owns the mechanism, the app owns the data\" split LoggerOptions uses for PSR-3 loggers."
order: 100
generated: true
---

`Io\PermissionProvider` · interface · namespace [`Io`](index.md)

```php
interface PermissionProvider
```

App-provided permission lookup for #[Permissions(...)], configured
via Io\Options\PermissionOptions::$provider (App::configure()),
the same "Io owns the mechanism, the app owns the data" split
LoggerOptions uses for PSR-3 loggers. Io has no ORM/database
layer of its own (deliberately; see the apps' own Repository
classes), so it has no way to know what a "permission" actually means
or where roles/permissions are stored; this interface is the entire
contract.

## Methods

### hasPermission()

```php
public function hasPermission(mixed $jwt, string $permission): bool
```

True if $jwt (the current request's decoded JWT payload, i.e.
Request::$jwt; shape entirely app-defined, see JWT::encode())
currently holds $permission.

**Parameters**

- `$jwt` `mixed`
- `$permission` `string`

**Returns** `bool`
