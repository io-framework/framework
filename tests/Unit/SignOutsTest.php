<?php

namespace Io\Tests\Unit;

use Io\SignOuts;
use PHPUnit\Framework\TestCase;

final class SignOutsTest extends TestCase
{
    protected function tearDown(): void
    {
        SignOuts::reset();
    }

    public function testEveryListenerHearsTheEndedSignIns(): void
    {
        $heard = [];
        SignOuts::listen(function (array $ids) use (&$heard): void {
            $heard[] = $ids;
        });
        SignOuts::listen(function (array $ids) use (&$heard): void {
            $heard[] = ['second', ...$ids];
        });

        SignOuts::publish(['7', '9']);
        SignOuts::publish([]);

        $this->assertSame([['7', '9'], ['second', '7', '9']], $heard);
    }
}
