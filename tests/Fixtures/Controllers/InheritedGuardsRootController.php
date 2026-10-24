<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\Authenticated;

/** Guards declared two levels up: PHP doesn't inherit attributes, the RouteCompiler must. */
#[Authenticated]
abstract class InheritedGuardsRootController
{
}
