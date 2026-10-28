---
title: "Body"
description: "Binds the request body to a method parameter: a DTO class (built by the Hydrator, then validated), for an `array` parameter with `of: OrderLine::class` a list of them, one per element, and for an `Io\\Form` parameter with `of: LoginForm::class` the DTO with its errors, which the method reads itself."
order: 100
generated: true
---

`Io\Attributes\Body` · class · namespace [`Io\Attributes`](index.md)

```php
class Body
```

Binds the request body to a method parameter: a DTO class (built by the
Hydrator, then validated), for an `array` parameter with `of: OrderLine::class`
a list of them, one per element, and for an `Io\Form` parameter with
`of: LoginForm::class` the DTO with its errors, which the method reads itself.
`validate: false` hands an invalid DTO to the method instead of answering 422;
a View it returns for an invalid DTO or Form still answers 422.

## Properties

### $name

```php
public ?string $name = null;
```

**Type** `string|null`

Unused for bodies; kept for symmetry with #[Param]/#[Query].

### $of

```php
public ?string $of = null;
```

**Type** `class-string|null`

Element DTO class for an `array`-typed parameter, the DTO class of a `Form` one.

### $validate

```php
public bool $validate = true;
```

**Type** `bool`

False: no 422 before the method runs; it reads `$dto->isValid` and `$dto->errors` itself.

## Methods

### __construct()

```php
public function __construct(
    public ?string $name = null,
    public ?string $of = null,
    public bool $validate = true,
)
```

**Parameters**

- `$name` `string|null`: Unused for bodies; kept for symmetry with #[Param]/#[Query].
- `$of` `class-string|null`: Element DTO class for an `array`-typed parameter, the DTO class of a `Form` one.
- `$validate` `bool`: False: no 422 before the method runs; it reads `$dto->isValid` and `$dto->errors` itself.
