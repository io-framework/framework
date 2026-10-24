<?php

namespace Io\Tests\Fixtures\Support;

use Io\Http\View;
use Io\Attributes\Inject;

/**
 * A View subclass with an #[Inject] property typed to a class that doesn't
 * exist. Exercises Router::injectViewProperties()'s !class_exists($type)
 * skip branch, which a plain View::$env (always resolvable) never reaches.
 */
class ViewWithUnresolvableInject extends View
{
    #[Inject]
    private \Io\Tests\Fixtures\Support\TotallyMadeUpUnresolvableClass $unresolvable;
}
