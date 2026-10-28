---
title: "CsrfOptions"
description: "Stateless double-submit-cookie CSRF protection (see Io\\Csrf)."
order: 100
generated: true
---

`Io\Options\CsrfOptions` · class · namespace [`Io\Options`](index.md)

```php
class CsrfOptions
```

Stateless double-submit-cookie CSRF protection (see Io\Csrf). Off
until $secret is set; #[SkipCsrf] opts a single route out.

## Properties

### $secret

```php
public string $secret;
```

HMAC key. Deliberately uninitialized: reading it unset throws instead of signing against a guessable default.

### $ttl

```php
public int $ttl = 3600;
```

Token lifetime in seconds (signed into the token, and the cookie's Max-Age).

### $cookieName

```php
public string $cookieName = 'csrf_token';
```

The cookie name. Not HttpOnly on purpose: client-side JS mirrors it into $headerName.

### $headerName

```php
public string $headerName = 'X-CSRF-Token';
```

Request header checked first (the fetch()/XHR path).

### $fieldName

```php
public string $fieldName = '_csrf';
```

Body field checked second (the plain `<form>` path, see Csrf::field()).

### $cookieSecure

```php
public ?bool $cookieSecure = null;
```

Cookie Secure attribute. Null (default): Secure when the request came over HTTPS (behind a trusted proxy, as it says).

### $cookieSameSite

```php
public string $cookieSameSite = 'Lax';
```

Cookie SameSite attribute; 'Lax' still sends it on a top-level navigation into a form.

## Methods

### keepSecretIn()

```php
public function keepSecretIn(string $file): void
```

Turns the protection on with a secret kept in $file: read from it, or on the first start 32 random
bytes written there as hex, readable by the owner only, so tokens survive a restart without a secret
in the environment. A secret set before wins; `$opts->secret = $env->string('CSRF_SECRET', '')` first
lets the environment decide in production. Plain file functions: it runs while the app is configured.

**Parameters**

- `$file` `string`
