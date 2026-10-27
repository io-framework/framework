---
title: "Length"
description: "Validation: a string property's length (mb_strlen) must be within [$min, $max]."
order: 100
generated: true
---

`Io\Attributes\Length` · class · namespace [`Io\Attributes`](index.md)

```php
class Length implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: a string property's length (mb_strlen) must be within [$min, $max]. Null passes, so an optional field
may have a minimum length; #[NotBlank] requires it. Any other type counts as length 0.

## Properties

### $min

```php
public int $min = 0;
```

### $max

```php
public int $max = 9223372036854775807;
```

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(
    public int $min = 0,
    public int $max = \Io\Attributes\PHP_INT_MAX,
    public ?string $message = null,
)
```

**Parameters**

- `$min` `int`
- `$max` `int`
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
