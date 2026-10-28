---
title: "Redirect"
description: "Returned from a route method to send a redirect instead of a body: `return new Redirect('/backoffice')` next to the View the same handler renders on failure (POST/redirect/GET)."
order: 100
generated: true
---

`Io\Http\Redirect` · final class · namespace [`Io\Http`](index.md)

```php
final class Redirect
```

Returned from a route method to send a redirect instead of a body:
`return new Redirect('/backoffice')` next to the View the same handler
renders on failure (POST/redirect/GET). Router turns it into status +
Location; cookies/headers already queued on the Response are kept.
A plain result, not an exception: a redirect is a normal outcome, and
`View|Redirect` on the signature says so. Auth failures that should
redirect to the login page go through UnauthorizedException + JWTOptions::$loginRedirectUrl, which hands
the page over as ?next=; Redirect::back() takes the visitor there after the login.

## Properties

### $location

```php
public readonly string $location;
```

**Type** `string`

Sent verbatim as the Location header.

### $status

```php
public readonly int $status = 302;
```

**Type** `int`

Any 3xx; 302 by default (303 for an explicit See Other, 301/308 permanent, 307 keeps the method).

## Methods

### __construct()

```php
public function __construct(public readonly string $location, public readonly int $status = 302)
```

**Parameters**

- `$location` `string`: Sent verbatim as the Location header.
- `$status` `int`: Any 3xx; 302 by default (303 for an explicit See Other, 301/308 permanent, 307 keeps the method).

### back()

```php
public static function back(
    \Io\Http\Request $req,
    string $default = '/',
    string $parameter = 'next',
    int $status = 303,
): \Io\Http\Redirect
```

Back to where the visitor came from: the path in the query's or the form's $parameter (`?next=`, a hidden
field), else $default. Only a path of this site is followed, so a link to the login page cannot send
someone elsewhere afterwards.

**Parameters**

- `$req` [`Request`](Request.md)
- `$default` `string`
- `$parameter` `string`
- `$status` `int`

**Returns** [`Redirect`](Redirect.md)

### isLocal()

```php
public static function isLocal(string $url): bool
```

A path of this site: one leading slash, then no second one and no backslash (browsers read both as
"another host"), no whitespace or control characters.

**Parameters**

- `$url` `string`

**Returns** `bool`
