---
title: "RequestOptions"
description: "Limits on what a request may bring."
order: 100
generated: true
---

`Io\Options\RequestOptions` · class · namespace [`Io\Options`](index.md)

```php
class RequestOptions
```

Limits on what a request may bring. Configure via App::configure().

## Properties

### $maxJsonBytes

```php
public int $maxJsonBytes = 262144;
```

A JSON body larger than this (bytes) is answered with 413 instead of decoded. Keys that collide in PHP's
array hash make json_decode() slow with the square of their number: 256 KiB of them take about 50 ms, 1 MiB
about 750 ms, and the worker serves nothing else meanwhile.
