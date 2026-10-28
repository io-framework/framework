---
title: "Request"
description: "The incoming HTTP request."
order: 100
generated: true
---

`Io\Http\Request` · class · namespace [`Io\Http`](index.md)

```php
class Request
```

The incoming HTTP request. capture() reads it from the superglobals (FPM, php -S); fromParts() builds it
from plain values, for a runtime that hands over requests itself instead of filling the superglobals.
Nothing after that reads the superglobals again.

## Properties

### $method

```php
public string $method;
```

### $path

```php
public string $path;
```

The path the router matches, and so what middleware should check: slashes at either end trimmed, each
segment percent-decoded (/my%20file.txt is "/my file.txt", /%C3%BCber is "/über"). A segment that decodes to
a slash or a NUL leaves the whole path encoded, so an encoded slash never makes a new segment.

### $pathEncoded

```php
public bool $pathEncoded = false;
```

Whether $path stayed percent-encoded (an encoded slash or NUL in it): the router decodes its placeholders then.

### $headers

```php
public array $headers = [];
```

**Type** `array<string, string>`

Header names normalized to "X-Some-Header" casing.

### $query

```php
public array $query = [];
```

**Type** `array<string, mixed>`

### $body

```php
public array $body = [] { get; set; }
```

**Type** `array<array-key, mixed>`

A JSON object/form body (string keys) or a JSON list body (int keys, see #[Body(of: ...)]). A JSON body is
decoded when it is first read, after routing and guards, so a request to no route never pays for it.

### $bodyError

```php
public ?string $bodyError = null { get; }
```

Why a JSON body could not be decoded (json_last_error_msg()), null when it could or there was none: #[Body] answers 400 then.

### $bodyTooLarge

```php
public bool $bodyTooLarge = false { get; }
```

Whether the JSON body was larger than $maxJsonBytes and so not decoded: #[Body] answers 413 then.

### $maxJsonBytes

```php
public int $maxJsonBytes = 262144;
```

The largest JSON body that is decoded; App::handle() sets it from RequestOptions::$maxJsonBytes.

### $cookies

```php
public array $cookies = [];
```

**Type** `array<string, string>`

The request cookies, name => value.

### $files

```php
public array $files = [];
```

**Type** `array<array-key, mixed>`

UploadedFile or arrays of them

Uploaded files of a multipart/form-data request, nested as the field names say (avatar, photos[], doc[a]).
A file input left empty is not in here.

### $https

```php
public bool $https = false;
```

Whether the request arrived over HTTPS; behind a proxy listed in ProxyOptions::$trusted, what its X-Forwarded-Proto says.

### $ip

```php
public string $ip = '';
```

The client's address: the connection's ('' if the runtime doesn't say), or behind a proxy listed in ProxyOptions::$trusted, what its X-Forwarded-For says.

### $jwt

```php
public ?array $jwt = null;
```

**Type** `array<string, mixed>|null`

The decoded JWT payload, set by useJwt() when the request carried a valid token; #[Claims] binds it as a DTO.

### $jwtFromCookie

```php
public bool $jwtFromCookie = false;
```

Whether $jwt came from the cookie (JWTOptions::$cookieName), which the browser sends on its own: a #[SkipCsrf] route ignores it.

### $bodyDto

```php
public ?object $bodyDto = null;
```

The #[Body] DTO the matched route built (set before validation runs, so present even when validation fails); null until routing has run.

### $id

```php
public string $id;
```

Correlation id: the incoming X-Request-Id (or X-Correlation-Id), else a fresh UUID v4. Echoed back as X-Request-ID and stamped onto log lines.

## Methods

### fromParts()

```php
public static function fromParts(
    string $method,
    string $uri,
    array $headers = [],
    string $body = '',
    array $cookies = [],
    bool $https = false,
    ?array $form = null,
    array $files = [],
    string $ip = '',
): \Io\Http\Request
```

A request from plain values. The query comes from $uri; the body is decoded as JSON when Content-Type
says so, else as a urlencoded form, unless $form already holds the parsed form fields (multipart).

**Parameters**

- `$method` `string`
- `$uri` `string`
- `$headers` `array<string, string>`: as sent, any casing (an underscore is not a dash)
- `$body` `string`
- `$cookies` `array<string, string>`
- `$https` `bool`
- `$form` `array<array-key, mixed>|null`
- `$files` `array<array-key, mixed>`: UploadedFile, nested as the field names say
- `$ip` `string`: The address the connection came from

**Returns** [`Request`](Request.md)

### capture()

```php
public static function capture(?string $rawBody = null): \Io\Http\Request
```

**Parameters**

- `$rawBody` `string|null`: Overrides php://input (which is always empty under a CLI test runner).

**Returns** [`Request`](Request.md)

### query()

```php
public function query(string $key, mixed $default = null): mixed
```

A query string value, or $default when the key is missing.

**Parameters**

- `$key` `string`
- `$default` `mixed`

**Returns** `mixed`

### string()

```php
public function string(string $key, string $default = ''): string
```

A query parameter as text; $default when it is missing or no text (`?q[]=x`).

**Parameters**

- `$key` `string`
- `$default` `string`

**Returns** `string`

### int()

```php
public function int(string $key, int $default = 0): int
```

A query parameter as a whole number (`?page=2`); $default when it is missing or no whole number.

**Parameters**

- `$key` `string`
- `$default` `int`

**Returns** `int`

### bool()

```php
public function bool(string $key, bool $default = false): bool
```

A query parameter as a switch: 1, true, on, yes or 0, false, off, no and empty; $default for anything else.

**Parameters**

- `$key` `string`
- `$default` `bool`

**Returns** `bool`

### strings()

```php
public function strings(string $key): array
```

A query parameter that may come several times (`?tag[]=a&tag[]=b`) or once (`?tag=a`), as a list of its texts.

**Parameters**

- `$key` `string`

**Returns** `list<string>`

### url()

```php
public function url(array $changes = []): string
```

This request's path and query with $changes applied, for a link to the same page: a value replaces the
parameter, null removes it. `$req->url(['lang' => 'de'])`, `$req->url(['page' => null])`; `$req->url()` is
the page as requested, a return target for the login.

**Parameters**

- `$changes` `array<string, scalar|list<scalar>|null>`

**Returns** `string`

### body()

```php
public function body(?string $key = null, mixed $default = null): mixed
```

The whole decoded body (JSON or form fields) without $key, else one field of it or $default.

**Parameters**

- `$key` `?string`
- `$default` `mixed`

**Returns** `($key is null ? array<array-key, mixed> : mixed)`

### file()

```php
public function file(string $name): ?\Io\Http\UploadedFile
```

The uploaded file of a field, named as in the form: "avatar", "doc[a]", "photos[1]"; null when there is none.

**Parameters**

- `$name` `string`

**Returns** `?`[`UploadedFile`](UploadedFile.md)
