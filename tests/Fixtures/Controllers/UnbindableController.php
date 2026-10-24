<?php

namespace Io\Tests\Fixtures\Controllers;

#[\Io\Attributes\Controller('/unbindable')]
final class UnbindableController
{
    #[\Io\Attributes\Get('')]
    public function show(\DateTimeImmutable $when): array
    {
        return [];
    }
}
