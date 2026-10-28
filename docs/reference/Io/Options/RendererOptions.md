---
title: "RendererOptions"
description: "Where view templates live and which layout wraps them."
order: 100
generated: true
---

`Io\Options\RendererOptions` · class · namespace [`Io\Options`](index.md)

```php
class RendererOptions
```

Where view templates live and which layout wraps them.

## Properties

### $directory

```php
public ?string $directory = null { get; set; }
```

Resolved like FileSystemOptions paths; null falls back to the running script's directory.

### $layout

```php
public string $layout = '_Layout';
```

Layout file name including .php ('_Layout.php'). The default has no extension and so matches no file: no layout until configured.

### $errorView

```php
public ?string $errorView = null;
```

A view (such as 'Error') that an error answers a browser page navigation with, in the layout, instead of
problem+json: a 404, a 403, the 500 of an exception. Its model is
`['status' => 404, 'title' => 'Not Found', 'detail' => ?string]`; the view picks its own words by the
status. fetch() calls and API clients keep the problem+json. Null (default): problem+json for everyone.

## Methods

### __construct()

```php
public function __construct()
```
