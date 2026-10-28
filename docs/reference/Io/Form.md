---
title: "Form"
description: "A DTO built from a request with what was wrong with it: what `#[Body(of: LoginForm::class)] Form $form` hands a method that shows a form again."
order: 100
generated: true
---

`Io\Form` · final class · namespace [`Io`](index.md)

```php
final class Form
```

A DTO built from a request with what was wrong with it: what `#[Body(of: LoginForm::class)] Form $form`
hands a method that shows a form again. The DTO stays a plain data object; the errors live here.

```php
if (!$form->valid) {
    return new View('Account/Login', $form);
}
$form->fail('password', 'Email or password do not match.');
```

**Template parameters**

- `T` of `object`

## Properties

### $errors

```php
public array $errors { get; }
```

**Type** `array<string, list<string>>`

field => messages: the rules first, then values that did not fit, required fields left out, nested DTOs'
errors ("address.city") and fail() entries.

### $valid

```php
public bool $valid { get; }
```

### $dto

```php
public readonly object $dto;
```

**Type** `T`

## Methods

### __construct()

```php
public function __construct(public readonly object $dto, private readonly array $failures = [])
```

**Parameters**

- `$dto` `T`
- `$failures` `array<string, list<string>>`: What the hydrator could not fit

### fail()

```php
public function fail(string $field, string $message): static
```

Records a failure the rules cannot know about (a taken email); it joins the field's other messages.

**Parameters**

- `$field` `string`
- `$message` `string`

**Returns** `static`
