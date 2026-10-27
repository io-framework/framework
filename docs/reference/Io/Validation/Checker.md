---
title: "Checker"
description: "A check that needs the app, named by #[Check]: a lookup in the database, a call to a service."
order: 100
generated: true
---

`Io\Validation\Checker` · interface · namespace [`Io\Validation`](index.md)

```php
interface Checker
```

A check that needs the app, named by #[Check]: a lookup in the database, a call to a service. It comes from
the container, so it takes its services through #[Inject].

## Methods

### passes()

```php
public function passes(mixed $value, object $dto): bool
```

**Parameters**

- `$value` `mixed`
- `$dto` `object`

**Returns** `bool`
