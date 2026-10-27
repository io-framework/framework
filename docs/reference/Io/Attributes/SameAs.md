---
title: "SameAs"
description: "Validation: the property must equal the DTO's $property (e.g."
order: 100
generated: true
---

`Io\Attributes\SameAs` · class · namespace [`Io\Attributes`](index.md)

```php
class SameAs implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: the property must equal the DTO's $property (e.g. a password confirmation).

## Properties

### $property

```php
public string $property;
```

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public string $property, public ?string $message = null)
```

**Parameters**

- `$property` `string`
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
