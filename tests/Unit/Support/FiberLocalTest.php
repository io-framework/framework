<?php

namespace Io\Tests\Unit\Support;

use Fiber;
use PHPUnit\Framework\TestCase;
use Io\Csrf;
use Io\Http\{Request, Response};
use Io\Options\CsrfOptions;
use Io\Support\{FiberLocal, LocalStorage};
use Io\App;

final class FiberLocalTest extends TestCase
{
    protected function tearDown(): void
    {
        FiberLocal::useStorage(null);
        App::reset();
    }

    public function testEachFiberSeesItsOwnValueAndOutsideAFiberIsOneSlot(): void
    {
        $local = new FiberLocal();
        $local->set('main');
        $seen = [];
        // A closure, not fn (): fn () would capture $seen by value
        $fiber = function (string $value) use ($local, &$seen): Fiber {
            return new Fiber(function () use ($local, $value, &$seen): void {
                $seen[] = $local->get();
                $local->set($value);
                Fiber::suspend();
                $seen[] = $local->get();
                $local->set(null);
                $seen[] = $local->get();
            });
        };

        $a = $fiber('a');
        $b = $fiber('b');
        $a->start();
        $b->start();
        $a->resume();
        $b->resume();

        // Without its own value a Fiber sees the main slot; forgetting its value falls back to it
        $this->assertSame(['main', 'main', 'a', 'main', 'b', 'main'], $seen);
        $this->assertSame('main', $local->get());
    }

    public function testTheCsrfTokenIsKeptPerFiber(): void
    {
        App::create()->configure(function (CsrfOptions $opts) {
            $opts->secret = 'test-secret-long-enough-for-hmac-32';
        });
        $csrf = new Csrf();
        $tokens = [];
        $fiber = function () use ($csrf, &$tokens): Fiber {
            return new Fiber(function () use ($csrf, &$tokens): void {
                $csrf->issueFor(Request::fromParts('GET', '/'), new Response());
                $issued = $csrf->token();
                Fiber::suspend();
                $tokens[] = [$issued, $csrf->token()];
            });
        };

        $a = $fiber();
        $b = $fiber();
        $a->start();
        $b->start();
        $a->resume();
        $b->resume();

        [[$aIssued, $aLater], [$bIssued, $bLater]] = $tokens;
        $this->assertNotSame($aIssued, $bIssued);
        $this->assertSame($aIssued, $aLater);
        $this->assertSame($bIssued, $bLater);
        $this->assertSame('', $csrf->token());
    }

    public function testARuntimesStorageTakesOverEveryFiberLocal(): void
    {
        $before = new FiberLocal();
        $before->get();
        // One shared slot stands in for a runtime's async context, which hands values on to child tasks
        $shared = new class implements LocalStorage {
            public mixed $value = null;

            public function get(): mixed
            {
                return $this->value;
            }

            public function set(mixed $value): void
            {
                $this->value = $value;
            }
        };
        FiberLocal::useStorage(fn () => $shared);
        $local = new FiberLocal();

        $local->set('request-1');
        $child = new Fiber(fn () => $local->get());
        $child->start();

        $this->assertSame('request-1', $child->getReturn());
        $this->assertSame('request-1', $shared->value);
        // A FiberLocal already used before (while the app booted) switches over too
        $before->set('own');
        $this->assertSame('own', $shared->value);
    }

    public function testCaptureSetsTheValuesOfEveryFiberLocalAgainLaterAndElsewhere(): void
    {
        $requestId = new FiberLocal();
        $locale = new FiberLocal();
        $unset = new FiberLocal();
        $requestId->set('req-1');
        $locale->set('de');
        $restore = FiberLocal::capture();

        $requestId->set(null);
        $locale->set('en');
        $restore();
        $this->assertSame(['req-1', 'de', null], [$requestId->get(), $locale->get(), $unset->get()]);

        // In a Fiber of its own, as the handler of an upgraded request
        $seen = null;
        $requestId->set(null);
        (new Fiber(function () use ($restore, $requestId, &$seen): void {
            $restore();
            $seen = $requestId->get();
        }))->start();
        $this->assertSame('req-1', $seen);
    }

    public function testCaptureUnsetsWhatWasUnsetWhenItWasTaken(): void
    {
        $user = new FiberLocal();
        $anonymous = FiberLocal::capture();
        $user->set('alice');
        $alice = FiberLocal::capture();
        $later = new FiberLocal();

        // The tab of an anonymous visitor renders after Alice's: it must not see her
        $alice();
        $later->set('alice-only');
        $anonymous();
        $this->assertSame([null, null], [$user->get(), $later->get()]);

        $alice();
        $this->assertSame(['alice', null], [$user->get(), $later->get()]);
    }
}
