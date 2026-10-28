<?php

namespace Io\View;

use Closure;
use Io\Html;
use Io\Http\{Request, View};

/**
 * Views rendered as markup without a layout, set up as a page's view is (helpers, CSRF, the request): what
 * `@include` does in a view, for a plugin that renders templates of its own (Live's components).
 */
final readonly class Partials
{
    /** @param Closure(View, ?Request): string $render */
    public function __construct(private Closure $render)
    {
    }

    public function render(string $view, mixed $model = null, ?Request $request = null): Html
    {
        return new Html(($this->render)(new View($view, $model, partial: true), $request));
    }
}
