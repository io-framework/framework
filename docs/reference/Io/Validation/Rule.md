---
title: "Rule"
description: "A validation attribute on a DTO property."
order: 100
generated: true
---

`Io\Validation\Rule` · interface · namespace [`Io\Validation`](index.md)

```php
interface Rule
```

A validation attribute on a DTO property. The Validator applies every attribute that implements it, in the
order they are written, and reports the message of each one that fails.

## Methods

### passes()

```php
public function passes(mixed $value, object $dto): bool
```

Whether $value passes; $dto is the whole object, for rules that compare with another property.

**Parameters**

- `$value` `mixed`
- `$dto` `object`

**Returns** `bool`

### message()

```php
public function message(mixed $value): string
```

The message for a $value that failed, with its {placeholders}: the rule's own `message:` or its English
text, which is also the catalog key.

**Parameters**

- `$value` `mixed`

**Returns** `string`

### params()

```php
public function params(): array
```

**Returns** `array<string, scalar>`: the values for the message's placeholders
