---
title: "FileFormatter"
description: "Sends the route's return value as a raw body instead of JSON: return an SplFileInfo to send a file from disk, a string to send that text."
order: 100
generated: true
---

`Io\Attributes\FileFormatter` · class · namespace [`Io\Attributes`](index.md)

```php
class FileFormatter
```

Sends the route's return value as a raw body instead of JSON: return an SplFileInfo to send a file from disk, a string to send that text.

## Properties

### $mimetype

```php
public ?string $mimetype = null;
```

### $filename

```php
public ?string $filename = null;
```

### $download

```php
public bool $download = false;
```

## Methods

### __construct()

```php
public function __construct(
    public ?string $mimetype = null,
    public ?string $filename = null,
    public bool $download = false,
)
```

**Parameters**

- `$mimetype` `?string`
- `$filename` `?string`
- `$download` `bool`
