<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get};
use Io\Http\View;

/** A plugin's view named like one of the app's: it must not get the app view's compiled CSS/JS. */
#[Controller('/plugin-view')]
class PluginViewController
{
    #[Get('')]
    public function show(): View
    {
        return new View('HomePage', layout: '_Layout.php', directory: __DIR__ . '/../PluginViews');
    }
}
