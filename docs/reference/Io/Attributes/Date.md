---
title: "Date"
description: "Validation: a string property must be a real date in $format (a date() format, 'Y-m-d' by default): \"2026-02-30\" fails."
order: 100
generated: true
---

`Io\Attributes\Date` · class · namespace [`Io\Attributes`](index.md)

```php
class Date implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: a string property must be a real date in $format (a date() format, 'Y-m-d' by default):
"2026-02-30" fails. Null passes, a DateTimeInterface always does.

## Properties

### $format

```php
public string $format = 'Y-m-d';
```

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public string $format = 'Y-m-d', public ?string $message = null)
```

**Parameters**

- `$format` `string`
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
