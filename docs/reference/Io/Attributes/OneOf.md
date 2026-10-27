---
title: "OneOf"
description: "Validation: a non-null property value must be one of $values (strict comparison)."
order: 100
generated: true
---

`Io\Attributes\OneOf` · class · namespace [`Io\Attributes`](index.md)

```php
class OneOf implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: a non-null property value must be one of $values (strict comparison).

## Properties

### $values

```php
public array $values;
```

**Type** `list<scalar>`

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public array $values, public ?string $message = null)
```

**Parameters**

- `$values` `list<scalar>`
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
