<?php

namespace Io\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Io\{App, Csrf, SignOuts};
use Io\Http\{Request, Response};
use Io\Options\{CsrfOptions, JWTOptions};

final class CsrfTest extends TestCase
{
    private array $serverBackup;
    private array $postBackup;
    private array $cookieBackup;

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
        $this->postBackup = $_POST;
        $this->cookieBackup = $_COOKIE;

        // PHP tracks "sent" headers process-wide, across the whole test
        // run. Clear them so a test reading back Set-Cookie only ever
        // sees what *it* sent (see IntegrationTestCase::dispatch()'s
        // identical reasoning).
        header_remove();

        App::reset();
        App::create()->configure(function (CsrfOptions $opts) {
            $opts->secret = 'test-secret-long-enough-for-hmac-32';
        });
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        $_POST = $this->postBackup;
        $_COOKIE = $this->cookieBackup;
        App::reset();
    }

    private function captureRequest(?string $rawBody = null): Request
    {
        return Request::capture($rawBody);
    }

    // generateToken() / isValidToken()

    public function testGenerateTokenProducesTwoBase64UrlSegments(): void
    {
        $token = Csrf::generateToken();
        $segments = explode('.', $token);

        $this->assertCount(2, $segments);
        foreach ($segments as $segment) {
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $segment);
        }
    }

    public function testGenerateTokenIsValid(): void
    {
        $this->assertTrue(Csrf::isValidToken(Csrf::generateToken()));
    }

    public function testGenerateTokenProducesADifferentTokenEachTime(): void
    {
        $this->assertNotSame(Csrf::generateToken(), Csrf::generateToken());
    }

    public function testIsValidTokenRejectsATamperedSignature(): void
    {
        [$payload] = explode('.', Csrf::generateToken());
        $tampered = $payload . '.' . 'not-a-valid-signature';

        $this->assertFalse(Csrf::isValidToken($tampered));
    }

    public function testIsValidTokenRejectsMalformedTokens(): void
    {
        $this->assertFalse(Csrf::isValidToken(null));
        $this->assertFalse(Csrf::isValidToken(''));
        $this->assertFalse(Csrf::isValidToken('not-a-csrf-token'));
        $this->assertFalse(Csrf::isValidToken('way.too.many.dots'));
    }

    public function testIsValidTokenRejectsATokenSignedWithADifferentSecret(): void
    {
        $token = Csrf::generateToken();

        App::create()->configure(function (CsrfOptions $opts) {
            $opts->secret = 'a-different-secret-long-enough-for-hmac';
        });

        $this->assertFalse(Csrf::isValidToken($token));
    }

    public function testATokenIsBoundToTheSessionCookieItWasMintedFor(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->cookieName = 'auth_token';
        });

        // The attacker's own session: a valid token for them
        $attackerToken = Csrf::generateToken('attacker-session');
        $this->assertTrue(Csrf::isValidToken($attackerToken, 'attacker-session'));

        // Planted into the victim's cookie jar and repeated in the forged form: it fails against the victim's session
        $_COOKIE['auth_token'] = 'victim-session';
        $_COOKIE['csrf_token'] = $attackerToken;
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $attackerToken;
        $this->assertFalse(Csrf::isValidToken($attackerToken, 'victim-session'));
        $this->assertFalse((new Csrf())->verify($this->captureRequest()));

        // The victim's own token still works
        $victimToken = Csrf::generateToken('victim-session');
        $_COOKIE['csrf_token'] = $victimToken;
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $victimToken;
        $this->assertTrue((new Csrf())->verify($this->captureRequest()));

        // Signing in or out changes the session: the old token is no longer valid
        $this->assertFalse(Csrf::isValidToken($victimToken));
    }

    public function testATokenIsBoundToTheSignInOfAPrincipalASessionCookieSignedIn(): void
    {
        // A session middleware: no JWT cookie, the principal names its sign-in in 'sid'
        $victim = $this->captureRequest();
        $victim->principal = ['sub' => 'ada', SignOuts::CLAIM => 'victim-sign-in'];
        $victim->principalFromCookie = true;

        // A token minted while signed out, or for the attacker's own sign-in, fails for the victim
        $planted = [Csrf::generateToken(), Csrf::generateToken('attacker-sign-in')];
        foreach ($planted as $token) {
            $victim->cookies['csrf_token'] = $token;
            $victim->headers['X-CSRF-Token'] = $token;
            $this->assertFalse((new Csrf())->verify($victim));
        }

        // The victim's own token works
        $own = Csrf::generateToken('victim-sign-in');
        $victim->cookies['csrf_token'] = $own;
        $victim->headers['X-CSRF-Token'] = $own;
        $this->assertTrue((new Csrf())->verify($victim));

        // A view rendered for the victim mints a token for that sign-in, not the signed-out one
        $victim->cookies = [];
        $res = new Response();
        (new Csrf())->issueFor($victim, $res);
        $this->assertTrue(Csrf::isValidToken($res->queuedCookie('csrf_token'), 'victim-sign-in'));
        $this->assertFalse(Csrf::isValidToken($res->queuedCookie('csrf_token')));
    }

    public function testTheLoginFormIsPostedSignedOutAndTheNextViewMintsATokenForTheSignIn(): void
    {
        // The login page was rendered signed out: its token belongs to no sign-in, and the login posts it so
        $token = Csrf::generateToken();
        $login = $this->captureRequest();
        $login->cookies['csrf_token'] = $token;
        $login->headers['X-CSRF-Token'] = $token;
        $this->assertTrue((new Csrf())->verify($login));

        // After the redirect the session middleware signs the request in: the old token no longer fits
        $next = $this->captureRequest();
        $next->cookies['csrf_token'] = $token;
        $next->principal = [SignOuts::CLAIM => 'new-sign-in'];
        $next->principalFromCookie = true;
        $res = new Response();
        (new Csrf())->issueFor($next, $res);
        $this->assertTrue(Csrf::isValidToken($res->queuedCookie('csrf_token'), 'new-sign-in'));
    }

    public function testAJwtCookieAndABearerPrincipalKeepTheirBinding(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->cookieName = 'auth_token';
        });

        // The JWT cookie stays what the token is bound to, even when its payload carries a 'sid'
        $req = $this->captureRequest();
        $req->cookies['auth_token'] = 'jwt-cookie';
        $req->principal = [SignOuts::CLAIM => 'sid-in-the-jwt'];
        $req->principalFromCookie = true;
        $token = Csrf::generateToken('jwt-cookie');
        $req->cookies['csrf_token'] = $token;
        $req->headers['X-CSRF-Token'] = $token;
        $this->assertTrue((new Csrf())->verify($req));

        // A bearer token is no cookie the browser sends on its own: its 'sid' binds nothing
        $bearer = $this->captureRequest();
        $bearer->principal = [SignOuts::CLAIM => 'sid-in-the-jwt'];
        $token = Csrf::generateToken();
        $bearer->cookies['csrf_token'] = $token;
        $bearer->headers['X-CSRF-Token'] = $token;
        $this->assertTrue((new Csrf())->verify($bearer));
    }

    public function testAResponseThatSignsSomeoneInCarriesATokenForTheNewSession(): void
    {
        App::create()->configure(function (JWTOptions $opts) {
            $opts->cookieName = 'auth_token';
        });
        // A login: the response sets the new session cookie
        $res = (new Response())->withCookie('auth_token', 'new-session');
        (new Csrf())->followSession($res);
        $issued = $res->queuedCookie('csrf_token');
        $this->assertNotNull($issued);

        // The next request carries both new cookies: the token verifies there
        $this->assertTrue(Csrf::isValidToken($issued, 'new-session'));

        // Logging out removes the session cookie: the token follows to "no session"
        $res = (new Response())->withoutCookie('auth_token');
        (new Csrf())->followSession($res);
        $this->assertTrue(Csrf::isValidToken($res->queuedCookie('csrf_token')));

        // A response that leaves the session alone issues nothing
        $res = new Response();
        (new Csrf())->followSession($res);
        $this->assertNull($res->queuedCookie('csrf_token'));
    }

    // verify()

    public function testVerifyAcceptsAMatchingCookieAndHeaderToken(): void
    {
        $token = Csrf::generateToken();
        $_COOKIE['csrf_token'] = $token;
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $token;

        $csrf = new Csrf();
        $this->assertTrue($csrf->verify($this->captureRequest()));
    }

    public function testVerifyAcceptsAMatchingCookieAndBodyFieldToken(): void
    {
        $token = Csrf::generateToken();
        $_COOKIE['csrf_token'] = $token;
        $_POST['_csrf'] = $token;

        $csrf = new Csrf();
        $this->assertTrue($csrf->verify($this->captureRequest()));
    }

    public function testVerifyPrefersTheHeaderOverTheBodyFieldWhenBothArePresent(): void
    {
        $token = Csrf::generateToken();
        $_COOKIE['csrf_token'] = $token;
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $token;
        $_POST['_csrf'] = 'a-completely-different-value';

        $csrf = new Csrf();
        $this->assertTrue($csrf->verify($this->captureRequest()));
    }

    public function testVerifyRejectsWhenNoCookieIsPresent(): void
    {
        $token = Csrf::generateToken();
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $token;

        $csrf = new Csrf();
        $this->assertFalse($csrf->verify($this->captureRequest()));
    }

    public function testVerifyRejectsWhenNeitherHeaderNorBodyFieldIsPresent(): void
    {
        $token = Csrf::generateToken();
        $_COOKIE['csrf_token'] = $token;

        $csrf = new Csrf();
        $this->assertFalse($csrf->verify($this->captureRequest()));
    }

    public function testVerifyRejectsWhenCookieAndSubmittedValueDoNotMatch(): void
    {
        $_COOKIE['csrf_token'] = Csrf::generateToken();
        $_SERVER['HTTP_X_CSRF_TOKEN'] = Csrf::generateToken();

        $csrf = new Csrf();
        $this->assertFalse($csrf->verify($this->captureRequest()));
    }

    public function testVerifyRejectsAnExpiredToken(): void
    {
        $opts = App::getConfig(CsrfOptions::class);
        $opts->ttl = -10;
        $expired = Csrf::generateToken();
        $opts->ttl = 3600;

        $_COOKIE['csrf_token'] = $expired;
        $_SERVER['HTTP_X_CSRF_TOKEN'] = $expired;

        $csrf = new Csrf();
        $this->assertFalse($csrf->verify($this->captureRequest()));
    }

    public function testVerifyRejectsAForgedCookieEvenIfItMatchesTheSubmittedValue(): void
    {
        // Same value on both sides, but neither is a genuine
        // isValidToken(), proving the double-submit *match* alone isn't
        // enough without the signature also checking out.
        $_COOKIE['csrf_token'] = 'forged-value';
        $_SERVER['HTTP_X_CSRF_TOKEN'] = 'forged-value';

        $csrf = new Csrf();
        $this->assertFalse($csrf->verify($this->captureRequest()));
    }

    public function testVerifyReturnsTrueWhenCsrfOptionsWasNeverConfigured(): void
    {
        // Router::dispatch() calls verify() automatically for every
        // state-changing request; an app that never opted into CSRF
        // protection at all (never configured CsrfOptions) must not have
        // every POST/PUT/PATCH/DELETE start failing.
        App::reset();
        App::create();

        $csrf = new Csrf();
        $this->assertTrue($csrf->verify($this->captureRequest()));
    }

    // issueFor() / token() / field()

    public function testIssueForSetsANonHttpOnlyCookieWhenNoneExistsYet(): void
    {
        $csrf = new Csrf();
        $req = Request::fromParts('GET', '/', https: true);
        $res = new Response();

        $csrf->issueFor($req, $res->forRequest($req));
        $res->finalize();

        ob_start();
        $res->send();
        $cookieLine = $this->firstSetCookieLine();
        ob_end_clean();

        $this->assertNotNull($cookieLine);
        $this->assertStringStartsWith('csrf_token=' . $csrf->token(), $cookieLine);
        $this->assertStringNotContainsString('HttpOnly', $cookieLine);
        $this->assertStringContainsString('Secure', $cookieLine);
        $this->assertStringContainsString('SameSite=Lax', $cookieLine);
        $this->assertStringContainsString('Max-Age=3600', $cookieLine);
    }

    public function testTheCookieIsSecureOnlyOverHttpsUnlessConfigured(): void
    {
        $plain = Request::fromParts('GET', '/');
        $res = (new Response())->forRequest($plain);
        (new Csrf())->issueFor($plain, $res);
        $this->assertStringNotContainsString('Secure', $res->getCookies()[0]);

        App::getConfig(CsrfOptions::class)->cookieSecure = true;
        $res = (new Response())->forRequest($plain);
        (new Csrf())->issueFor($plain, $res);
        $this->assertStringContainsString('Secure', $res->getCookies()[0]);
    }

    public function testIssueForReusesAnExistingValidCookieInsteadOfRotating(): void
    {
        $existing = Csrf::generateToken();
        $_COOKIE['csrf_token'] = $existing;

        $csrf = new Csrf();
        $res = new Response();
        $csrf->issueFor($this->captureRequest(), $res);

        ob_start();
        $res->send();
        $cookieLine = $this->firstSetCookieLine();
        ob_end_clean();

        // No new Set-Cookie at all: the browser already has this exact
        // value, nothing to re-issue.
        $this->assertNull($cookieLine);
        $this->assertSame($existing, $csrf->token());
    }

    public function testIssueForMintsAFreshCookieWhenTheExistingOneIsExpired(): void
    {
        $opts = App::getConfig(CsrfOptions::class);
        $opts->ttl = -10;
        $expired = Csrf::generateToken();
        $opts->ttl = 3600;
        $_COOKIE['csrf_token'] = $expired;

        $csrf = new Csrf();
        $res = new Response();
        $csrf->issueFor($this->captureRequest(), $res);

        ob_start();
        $res->send();
        $cookieLine = $this->firstSetCookieLine();
        ob_end_clean();

        $this->assertNotNull($cookieLine);
        $this->assertNotSame($expired, $csrf->token());
        $this->assertTrue(Csrf::isValidToken($csrf->token()));
    }

    public function testTokenReturnsEmptyStringBeforeIssueForHasRun(): void
    {
        $csrf = new Csrf();
        $this->assertSame('', $csrf->token());
    }

    public function testFieldEmbedsTheTokenAsAnEscapedHiddenInput(): void
    {
        $csrf = new Csrf();
        $csrf->issueFor($this->captureRequest(), new Response());

        $field = $csrf->field();

        $this->assertStringContainsString('type="hidden"', $field);
        $this->assertStringContainsString('name="_csrf"', $field);
        $this->assertStringContainsString('value="' . htmlspecialchars($csrf->token(), ENT_QUOTES) . '"', $field);
    }

    public function testResetClearsTheCurrentToken(): void
    {
        $csrf = new Csrf();
        $csrf->issueFor($this->captureRequest(), new Response());
        $this->assertNotSame('', $csrf->token());

        $csrf->reset();

        $this->assertSame('', $csrf->token());
    }

    public function testIssueForDoesNothingWhenCsrfOptionsWasNeverConfigured(): void
    {
        App::reset();
        App::create();

        $csrf = new Csrf();
        $res = new Response();
        $csrf->issueFor($this->captureRequest(), $res);

        ob_start();
        $res->send();
        $cookieLine = $this->firstSetCookieLine();
        ob_end_clean();

        $this->assertNull($cookieLine);
        $this->assertSame('', $csrf->token());
    }

    private function firstSetCookieLine(): ?string
    {
        $raw = function_exists('xdebug_get_headers') ? xdebug_get_headers() : headers_list();
        foreach ($raw as $line) {
            if (stripos($line, 'Set-Cookie:') === 0) {
                return trim(substr($line, strlen('Set-Cookie:')));
            }
        }
        return null;
    }

    public function testKeepSecretInWritesASecretOnceAndReadsItAfterwards(): void
    {
        $dir = sys_get_temp_dir() . '/io-csrf-' . bin2hex(random_bytes(4));
        $file = "$dir/data/csrf-secret";
        try {
            $first = new CsrfOptions();
            $first->keepSecretIn($file);
            $second = new CsrfOptions();
            $second->keepSecretIn($file);

            $this->assertSame(64, strlen($first->secret));
            $this->assertSame($first->secret, $second->secret);
            $this->assertSame(0600, fileperms($file) & 0777);

            $configured = new CsrfOptions();
            $configured->secret = 'from-the-environment-long-enough-32';
            $configured->keepSecretIn($file);
            $this->assertSame('from-the-environment-long-enough-32', $configured->secret);
        } finally {
            @unlink($file);
            @rmdir("$dir/data");
            @rmdir($dir);
        }
    }
}
