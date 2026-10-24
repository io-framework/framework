---
title: "PublicFileServer"
description: "Serves the app's own static files from FileSystemOptions::$publicDirectory, and only from there: a path with a '..' or any other dot-segment, a dotfile or dot-directory (.env, .git/), or a PHP source file is never served, and the resolved file (symlinks followed) must lie inside the directory itself, not merely start with its name, which a sibling like `public-backup` would."
order: 100
generated: true
---

`Io\Http\PublicFileServer` · final class · namespace [`Io\Http`](index.md)

```php
final class PublicFileServer
```

Serves the app's own static files from FileSystemOptions::$publicDirectory, and only from there:
a path with a '..' or any other dot-segment, a dotfile or dot-directory (.env, .git/), or a PHP
source file is never served, and the resolved file (symlinks followed) must lie inside the
directory itself, not merely start with its name, which a sibling like `public-backup` would.

## Methods

### __construct()

```php
public function __construct(private \Io\Options\FileSystemOptions $options)
```

**Parameters**

- `$options` [`FileSystemOptions`](../Options/FileSystemOptions.md)

### serve()

```php
public function serve(string $path, \Io\Http\Response $res, ?\Io\Http\Request $req = null): bool
```

**Parameters**

- `$path` `string`
- `$res` [`Response`](Response.md)
- `$req` [`Request`](Request.md)`|null`: Its If-Modified-Since answers 304 for a file not changed since.

**Returns** `bool`
