---
title: "RouteAttribute"
description: "Contract of the HTTP-method route attributes (Get, Post, Put, Patch, Delete)."
order: 100
generated: true
---

`Io\Attributes\RouteAttribute` · interface · namespace [`Io\Attributes`](index.md)

```php
interface RouteAttribute
```

Contract of the HTTP-method route attributes (Get, Post, Put, Patch, Delete).

## Methods

### getHttpMethod()

```php
public function getHttpMethod(): string
```

**Returns** `string`

### getPath()

```php
public function getPath(): string
```

**Returns** `string`
