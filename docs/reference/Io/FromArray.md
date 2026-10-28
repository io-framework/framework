---
title: "FromArray"
description: "An array constructor and validation inside the DTO, the older way to take a request body: the Hydrator (and `#[Body(of: Dto::class)] Form $form`) does the same for a plain class."
order: 100
generated: true
---

`Io\FromArray` · trait · namespace [`Io`](index.md)

```php
trait FromArray
```

An array constructor and validation inside the DTO, the older way to take a request body: the Hydrator (and
`#[Body(of: Dto::class)] Form $form`) does the same for a plain class. Each key that names a publicly writable
instance property is assigned, unknown keys are ignored, and a value that
doesn't fit the property's type is left out and recorded as a failure (a 422,
not a TypeError 500, and never cast: "false" is no true). A form's text for a
number or a boolean ("5", "on") is converted, a backed enum property gets the
case its value names, and a property typed with another FromArray DTO (or an
array whose #[ListOf(Dto::class)] says so) is built from the nested array. Private, protected, protected(set)/
private(set) and static properties are never touched: the data comes from
the request, and those are the DTO's own state.

Validation is read straight off the DTO: `$dto->isValid` and `$dto->errors`
apply the rule attributes on every read, and `fail()` adds a failure a
controller found itself (a taken email, say) to the same list. Every failing
rule reports its message, so a field has a list of them. A writable property
without a default that the data left unset is "This value is required.".

## Properties

### $errors

```php
public array $errors { get; }
```

**Type** `array<string, list<string>>`

field => messages; attribute rules first, then fail() entries, required fields left unset, and nested DTOs' errors ("address.city").

### $isValid

```php
public bool $isValid { get; }
```

## Methods

### __construct()

```php
public function __construct(array $data = [])
```

**Parameters**

- `$data` `array<string, mixed>`

### fail()

```php
public function fail(string $field, string $message): static
```

Records a failure the attributes can't know about; it joins the field's other messages.

**Parameters**

- `$field` `string`
- `$message` `string`

**Returns** `static`

### hydrateFromArray()

```php
protected function hydrateFromArray(array $data): void
```

Split out so a using class can declare its own __construct() and still call this.

**Parameters**

- `$data` `array<string, mixed>`

### fromArrayExcludes()

```php
protected function fromArrayExcludes(): array
```

Property names hydration skips (e.g. a nested DTO set some other way). A method, since a trait property's default can't be overridden.

**Returns** `array<int, string>`
