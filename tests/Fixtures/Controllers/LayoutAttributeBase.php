<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\Layout;

/** The parent of LayoutAttributeController: its #[Layout] reaches every route of the child. */
#[Layout('AltLayout.php')]
abstract class LayoutAttributeBase
{
}
