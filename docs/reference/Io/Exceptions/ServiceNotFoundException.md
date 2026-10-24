---
title: "ServiceNotFoundException"
description: "Thrown by Container::get() for a service it has not registered and cannot construct: a logged 500, never a 404 naming the class."
order: 100
generated: true
---

`Io\Exceptions\ServiceNotFoundException` · class · namespace [`Io\Exceptions`](index.md)

```php
class ServiceNotFoundException extends \Io\Exceptions\ContainerException implements \Psr\Container\NotFoundExceptionInterface
```

Extends [`ContainerException`](ContainerException.md)  
Implements `Psr\Container\NotFoundExceptionInterface`

Thrown by Container::get() for a service it has not registered and cannot construct: a logged 500, never a 404 naming the class.
