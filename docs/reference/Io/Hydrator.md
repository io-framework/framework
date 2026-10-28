---
title: "Hydrator"
description: "Builds a DTO from request data and keeps what did not fit, for #[Body] and Form."
order: 100
generated: true
---

`Io\Hydrator` · final class · namespace [`Io`](index.md)

```php
final class Hydrator
```

Builds a DTO from request data and keeps what did not fit, for #[Body] and Form. A value is never cast: it is
taken when it fits the property's type, converted when it is a form's text for a number or a boolean ("5",
"on"), a backed enum's value or the array of a nested DTO, and otherwise reported ("false" is no true, 2.7 no
2). #[Trim] trims text first.

- A DTO with constructor parameters gets them by name, promoted ones as well; when a required one is missing or
  does not fit, it is built without its constructor, with the values that fit, so a form can show it again.
- Any other DTO is created without arguments and its public, publicly settable properties are filled; private,
  protected, protected(set), private(set), static and #[Ignore] ones never are.
- A FromArray DTO, and one whose constructor takes the array (`__construct(array $data)`), builds itself.

What it learns about a class (the properties, their types, #[ListOf], #[Trim]) is kept for the life of the process.

## Methods

### form()

```php
public static function form(string $class, array $data): \Io\Form
```

**Parameters**

- `$class` `class-string<T>`
- `$data` `array<array-key, mixed>`

**Returns** [`Form`](Form.md)`<T>`

**Template parameters**

- `T` of `object`

### make()

```php
public static function make(string $class, array $data): array
```

The DTO and what did not fit it, by field

**Parameters**

- `$class` `class-string<T>`
- `$data` `array<array-key, mixed>`

**Returns** `array{T, array<string, list<string>>}`

**Template parameters**

- `T` of `object`

### hydrate()

```php
public static function hydrate(object $target, array $data, array $excludes = []): array
```

Fills $target's input properties from $data; returns what did not fit, by field.

**Parameters**

- `$target` `object`
- `$data` `array<array-key, mixed>`
- `$excludes` `array<int, string>`: Properties left alone

**Returns** `array<string, list<string>>`

### errorsOf()

```php
public static function errorsOf(object $dto, array $failures = []): array
```

Everything wrong with a DTO it built: the rules of its attributes, $failures, a required property left
unset, and the errors of nested DTOs ("address.city", "lines.0.qty").

**Parameters**

- `$dto` `object`
- `$failures` `array<string, list<string>>`

**Returns** `array<string, list<string>>`

### missingRequired()

```php
public static function missingRequired(object $dto, array $failures, array $excludes = []): array
```

A publicly writable property without a default that nothing set: required, and reading it would throw. A
value the data had but that did not fit already failed, so it is not reported a second time.

**Parameters**

- `$dto` `object`
- `$failures` `array<string, list<string>>`
- `$excludes` `array<int, string>`

**Returns** `array<string, list<string>>`

### nestedErrors()

```php
public static function nestedErrors(object $dto): array
```

The errors of nested DTOs, a property's or a list's, under "property.field" or "property.index.field".

**Parameters**

- `$dto` `object`

**Returns** `array<string, list<string>>`

### usesFromArray()

```php
public static function usesFromArray(string $class): bool
```

Whether $class uses FromArray, itself or through a parent

**Parameters**

- `$class` `string`

**Returns** `bool`

### fitting()

```php
public static function fitting(?\ReflectionType $type, mixed $value): array
```

Whether $value fits $type as it is, or as the text a form sends for a number or a boolean, a backed enum's
value, or the array of a nested DTO; the value to assign then, and why it doesn't fit when there is more to
say than its type.

**Parameters**

- `$type` `?`[`ReflectionType`](https://www.php.net/manual/en/class.reflectiontype.php)
- `$value` `mixed`

**Returns** `array{bool, mixed, ?string}`
