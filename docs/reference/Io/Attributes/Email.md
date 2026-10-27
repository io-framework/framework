---
title: "Email"
description: "Validation: a non-null property must be a syntactically valid email address."
order: 100
generated: true
---

`Io\Attributes\Email` · class · namespace [`Io\Attributes`](index.md)

```php
class Email implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: a non-null property must be a syntactically valid email address.

## Properties

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public ?string $message = null)
```

**Parameters**

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
