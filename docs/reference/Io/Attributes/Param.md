---
title: "Param"
description: "Binds a route placeholder to a controller method parameter, e.g."
order: 100
generated: true
---

`Io\Attributes\Param` · class · namespace [`Io\Attributes`](index.md)

```php
class Param
```

Binds a route placeholder to a controller method parameter, e.g.
`#[Get('/products/{slug}')]` with `function product(#[Param] string $slug)`.
By default the parameter name is matched against the placeholder name;
pass $name to bind to a differently-named placeholder.

## Properties

### $name

```php
public ?string $name = null;
```

**Type** `string|null`

Placeholder name to bind, if different from the parameter's own name.

## Methods

### __construct()

```php
public function __construct(public ?string $name = null)
```

**Parameters**

- `$name` `?string`
