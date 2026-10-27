---
title: "Validator"
description: "Validates a DTO's public properties against their validation attributes: every attribute that implements Rule (#[NotBlank], #[Email], #[Length], #[Regex], #[OneOf], #[SameAs], #[Date], #[Before], #[After], #[Min], #[Max], #[Check], or one of the app's own)."
order: 100
generated: true
---

`Io\Validator` · class · namespace [`Io`](index.md)

```php
class Validator
```

Validates a DTO's public properties against their validation attributes: every attribute that implements
Rule (#[NotBlank], #[Email], #[Length], #[Regex], #[OneOf], #[SameAs], #[Date], #[Before], #[After], #[Min],
#[Max], #[Check], or one of the app's own). The rules per class are reflected once and cached, so validating
the same DTO class on every request costs no reflection.

## Methods

### validate()

```php
public function validate(object $dto): array
```

**Parameters**

- `$dto` `object`

**Returns** `array<string, list<string>>`: field => the message of every rule that failed, in the order the rules are written

### merge()

```php
public static function merge(array ...$lists): array
```

Lists of messages per field, put together: $b's messages follow $a's.

**Parameters**

- `...$lists` `array<string, list<string>>`

**Returns** `array<string, list<string>>`
