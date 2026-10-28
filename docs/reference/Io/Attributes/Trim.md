---
title: "Trim"
description: "Trims the text a request sends for a property before it is checked: `\" ada \"` is `ada` for #[Length] and in the DTO."
order: 100
generated: true
---

`Io\Attributes\Trim` · class · namespace [`Io\Attributes`](index.md)

```php
class Trim
```

Trims the text a request sends for a property before it is checked: `"  ada "` is `ada` for #[Length] and in
the DTO. On a class, every string property of it; `#[Trim(false)]` on a property keeps its spaces (a password).

## Properties

### $trim

```php
public bool $trim = true;
```

## Methods

### __construct()

```php
public function __construct(public bool $trim = true)
```

**Parameters**

- `$trim` `bool`
