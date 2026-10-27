---
title: "Before"
description: "Validation: the date must lie before $date, which takes what DateTimeImmutable takes ('today', '-18 years', '2000-01-01')."
order: 100
generated: true
---

`Io\Attributes\Before` · class · namespace [`Io\Attributes`](index.md)

```php
class Before implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: the date must lie before $date, which takes what DateTimeImmutable takes ('today', '-18 years',
'2000-01-01'). Null passes, and so does a value that is no date at all: #[Date] reports that.

## Properties

### $date

```php
public string $date;
```

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public string $date, public ?string $message = null)
```

**Parameters**

- `$date` `string`
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
