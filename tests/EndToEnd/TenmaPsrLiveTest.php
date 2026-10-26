<?php

namespace Io\Tests\EndToEnd;

/**
 * The whole live suite once more under Tenma, but through PSR-15: Tenma's request becomes a PSR-7 one
 * (tenma/psr), Io\Psr\Handler turns it into Io's and back. WebSocket and stream included.
 */
final class TenmaPsrLiveTest extends TenmaLiveTest
{
    public static function setUpBeforeClass(): void
    {
        putenv('E2E_PSR=1');
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        putenv('E2E_PSR');
    }

    public function testUnderTenmasOwnTlsCookiesAreSecure(): void
    {
        $this->markTestSkipped('tenma/psr builds an http:// URI even over Tenma\'s own TLS');
    }
}
