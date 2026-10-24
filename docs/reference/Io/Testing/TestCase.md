---
title: "TestCase"
description: "Base class for app and plugin tests: a fresh Io per test, clean superglobals, and a request builder that drives App::handle() through Request::fromParts(), without header()/echo."
order: 100
generated: true
---

`Io\Testing\TestCase` · abstract class · namespace [`Io\Testing`](index.md)

```php
abstract class TestCase extends \PHPUnit\Framework\TestCase
```

Extends `PHPUnit\Framework\TestCase`

Base class for app and plugin tests: a fresh Io per test, clean
superglobals, and a request builder that drives App::handle() through
Request::fromParts(), without header()/echo.

  $this->request('POST', '/backoffice/login')->withBody(['email' => 'ben@sonnenkorn.test', 'password' => 'wrong'])->withCsrf()->send()
      ->assertStatus(422)->assertSee('Wrong email or password.');
  $this->request('GET', '/backoffice')->withJwt(['sub' => 2, 'role' => 'staff'])->send()->assertOk();

## Methods

### setUp()

```php
protected function setUp(): void
```

This method is called before each test.

### tearDown()

```php
protected function tearDown(): void
```

This method is called after each test.

### app()

```php
protected function app(): \Io\App
```

The app under test (created on first use). Configure and attach it here or in setUp().

**Returns** [`App`](../App.md)

### request()

```php
protected function request(string $method, string $uri): \Io\Testing\TestRequest
```

**Parameters**

- `$method` `string`
- `$uri` `string`

**Returns** [`TestRequest`](TestRequest.md)

### get()

```php
protected function get(string $uri, array $headers = []): \Io\Testing\TestResponse
```

**Parameters**

- `$uri` `string`
- `$headers` `array<string, string>`

**Returns** [`TestResponse`](TestResponse.md)

### post()

```php
protected function post(string $uri, array $body = [], array $headers = []): \Io\Testing\TestResponse
```

**Parameters**

- `$uri` `string`
- `$body` `array<array-key, mixed>`: Form fields.
- `$headers` `array<string, string>`

**Returns** [`TestResponse`](TestResponse.md)

### json()

```php
protected function json(string $method, string $uri, array $data = [], array $headers = []): \Io\Testing\TestResponse
```

A JSON request: $data is sent as the raw body with Content-Type: application/json.

**Parameters**

- `$method` `string`
- `$uri` `string`
- `$data` `array<array-key, mixed>`
- `$headers` `array<string, string>`

**Returns** [`TestResponse`](TestResponse.md)

### tempDir()

```php
protected function tempDir(): string
```

A fresh empty directory, removed after the test, e.g. for FileSystemOptions::$cacheDirectory.

**Returns** `string`
