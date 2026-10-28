---
title: "ViewHelpers"
description: "Every plugin's view helpers, merged, as a service: what a view calls as `$this->name()`, for a plugin that renders templates of its own (Live's components) and wants to offer the same."
order: 100
generated: true
---

`Io\Plugin\ViewHelpers` · final readonly class · namespace [`Io\Plugin`](index.md)

```php
final readonly class ViewHelpers
```

Every plugin's view helpers, merged, as a service: what a view calls as `$this->name()`, for a plugin that
renders templates of its own (Live's components) and wants to offer the same.

## Methods

### __construct()

```php
public function __construct(private readonly array $helpers)
```

**Parameters**

- `$helpers` `array<string, callable>`: name => callable

### has()

```php
public function has(string $name): bool
```

**Parameters**

- `$name` `string`

**Returns** `bool`

### get()

```php
public function get(string $name): callable
```

The helper; throws for a name no plugin offers, as a view does

**Parameters**

- `$name` `string`

**Returns** `callable`

### all()

```php
public function all(): array
```

**Returns** `array<string, callable>`
