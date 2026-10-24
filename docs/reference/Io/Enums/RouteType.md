---
title: "RouteType"
description: "The kind of a compiled plan Router::addCompiledRoute() stores: a static path, a path with placeholders, or a CLI task."
order: 100
generated: true
---

`Io\Enums\RouteType` · enum · namespace [`Io\Enums`](index.md)

```php
enum RouteType: string
```

The kind of a compiled plan Router::addCompiledRoute() stores: a static path, a path with placeholders, or a CLI task.

## Cases

### Static

```php
case Static = 'static';
```

### Dynamic

```php
case Dynamic = 'dynamic';
```

### Task

```php
case Task = 'task';
```
