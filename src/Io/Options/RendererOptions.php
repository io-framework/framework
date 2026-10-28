<?php

namespace Io\Options;

/** Where view templates live and which layout wraps them. */
class RendererOptions
{
    /** Resolved like FileSystemOptions paths; null falls back to the running script's directory. */
    public ?string $directory = null {
        get => $this->directory;
        set(?string $value) => $this->directory = $value !== null ? (new FileSystemOptions())->buildPath($value) : null;
    }

    /** Layout file name including .php ('_Layout.php'). The default has no extension and so matches no file: no layout until configured. */
    public string $layout = '_Layout';

    /**
     * A view (such as 'Error') that an error answers a browser page navigation with, in the layout, instead of
     * problem+json: a 404, a 403, the 500 of an exception. Its model is
     * `['status' => 404, 'title' => 'Not Found', 'detail' => ?string]`; the view picks its own words by the
     * status. fetch() calls and API clients keep the problem+json. Null (default): problem+json for everyone.
     */
    public ?string $errorView = null;

    public function __construct()
    {
        // Through the setter, so the directory starts as the script's directory, like any relative path.
        $this->directory = (new FileSystemOptions())->buildPath('');
    }
}
