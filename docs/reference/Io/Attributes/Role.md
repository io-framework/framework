---
title: "Role"
description: "Requires `$req->jwt['role']` to equal $role."
order: 100
generated: true
---

`Io\Attributes\Role` · class · namespace [`Io\Attributes`](index.md)

```php
class Role
```

Requires `$req->jwt['role']` to equal $role. On the class (every route on
it, also those of subclasses) or a single method; the method's wins if both
are present, the same precedence #[Version] and #[Sunset] use. Implies
#[Authenticated]: a request without a valid JWT gets UnauthorizedException
(401) before the role is compared, and a role mismatch throws
Io\Exceptions\ForbiddenException (403).

Assumes the app's own JWT payload carries a 'role' claim (nothing in
Io enforces that shape when issuing tokens; JWT::encode()
accepts any payload). For anything more structured than "one role
string", see #[Permissions] instead.

## Properties

### $role

```php
public string $role;
```

## Methods

### __construct()

```php
public function __construct(public string $role)
```

**Parameters**

- `$role` `string`
