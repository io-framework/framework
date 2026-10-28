---
title: "Partials"
description: "Views rendered as markup without a layout, set up as a page's view is (helpers, CSRF, the request): what `@include` does in a view, for a plugin that renders templates of its own (Live's components)."
order: 100
generated: true
---

`Io\View\Partials` · final readonly class · namespace [`Io\View`](index.md)

```php
final readonly class Partials
```

Views rendered as markup without a layout, set up as a page's view is (helpers, CSRF, the request): what
`@include` does in a view, for a plugin that renders templates of its own (Live's components).

## Methods

### __construct()

```php
public function __construct(private readonly \Closure $render)
```

**Parameters**

- `$render` [`Closure`](https://www.php.net/manual/en/class.closure.php)`(`[`View`](../Http/View.md)`, ?`[`Request`](../Http/Request.md)`): string`

### render()

```php
public function render(string $view, mixed $model = null, ?\Io\Http\Request $request = null): \Io\Html
```

**Parameters**

- `$view` `string`
- `$model` `mixed`
- `$request` `?`[`Request`](../Http/Request.md)

**Returns** [`Html`](../Html.md)
