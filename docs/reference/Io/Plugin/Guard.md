---
title: "Guard"
description: "Runs before the controller, after the core auth/CSRF checks."
order: 100
generated: true
---

`Io\Plugin\Guard` · interface · namespace [`Io\Plugin`](index.md)

```php
interface Guard
```

Runs before the controller, after the core auth/CSRF checks. Throw an HttpException to stop the request.

## Methods

### check()

```php
public function check(array $plan, \Io\Http\Request $req, \Io\Http\Response $res): void
```

**Parameters**

- `$plan` `array<string, mixed>`: What this plugin's RouteAttributeCompiler stored for the route ([] if nothing).
- `$req` [`Request`](../Http/Request.md)
- `$res` [`Response`](../Http/Response.md)
