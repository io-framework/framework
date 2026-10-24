<?php

namespace Io\Tests\Fixtures\Services;

use Io\Attributes\Inject;
use Io\Options\FileSystemOptions;

/**
 * A container-managed service that #[Inject]s an Options class. Proves
 * App::initDependencyInjection() reuses an Options instance the app already
 * configure()d (before attach()) instead of clobbering it with a fresh,
 * unconfigured one just because discoverAllClasses() also found it here.
 */
class ServiceWithInjectedOptions
{
    #[Inject]
    private FileSystemOptions $fileSystemOptions;

    public function getFileSystemOptions(): ?FileSystemOptions
    {
        return isset($this->fileSystemOptions) ? $this->fileSystemOptions : null;
    }
}
