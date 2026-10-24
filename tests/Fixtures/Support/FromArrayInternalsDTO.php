<?php

namespace Io\Tests\Fixtures\Support;

use Io\FromArray;

/** One public field plus internal state in every non-public shape a request must never reach (see FromArrayTest). */
class FromArrayInternalsDTO
{
    use FromArray;

    public string $name = '';
    private bool $isAdmin = false;
    protected int $credits = 0;
    public protected(set) string $owner = 'server';
    public private(set) bool $verified = false;
    public static string $shared = 'static';

    /** @return array{isAdmin: bool, credits: int} */
    public function internals(): array
    {
        return ['isAdmin' => $this->isAdmin, 'credits' => $this->credits];
    }
}
