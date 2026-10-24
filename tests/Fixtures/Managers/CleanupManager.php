<?php

namespace Io\Tests\Fixtures\Managers;

use Io\Attributes\{Manager, Task};

#[Manager('cleanup')]
class CleanupManager
{
    #[Task('ping')]
    public function ping(array $argv, int $argc): string
    {
        return 'cleanup-pong';
    }
}
