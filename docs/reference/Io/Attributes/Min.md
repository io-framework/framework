---
title: "Min"
description: "Validation: a number must be at least $value."
order: 100
generated: true
---

`Io\Attributes\Min` · class · namespace [`Io\Attributes`](index.md)

```php
class Min implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: a number must be at least $value. Null passes, and so does anything that is no number.

## Properties

### $value

```php
public int|float $value;
```

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public int|float $value, public ?string $message = null)
```

**Parameters**

- `$value` `int|float`
- `$message` `?string`

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
