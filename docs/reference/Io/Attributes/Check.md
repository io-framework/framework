---
title: "Check"
description: "Validation: the Checker $checker must pass the value, for what needs the app (a code that has to exist in the database)."
order: 100
generated: true
---

`Io\Attributes\Check` · class · namespace [`Io\Attributes`](index.md)

```php
class Check implements \Io\Validation\Rule
```

Implements [`Rule`](../Validation/Rule.md)

Validation: the Checker $checker must pass the value, for what needs the app (a code that has to exist in the
database). The checker comes from the container, so it takes its services through #[Inject].

## Properties

### $checker

```php
public string $checker;
```

**Type** `class-string<`[`Checker`](../Validation/Checker.md)`>`

### $message

```php
public ?string $message = null;
```

## Methods

### __construct()

```php
public function __construct(public string $checker, public ?string $message = null)
```

**Parameters**

- `$checker` `class-string<`[`Checker`](../Validation/Checker.md)`>`
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
