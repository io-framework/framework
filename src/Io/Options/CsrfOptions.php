<?php

namespace Io\Options;

use RuntimeException;

/**
 * Stateless double-submit-cookie CSRF protection (see Io\Csrf). Off
 * until $secret is set; #[SkipCsrf] opts a single route out.
 */
class CsrfOptions
{
    /** HMAC key. Deliberately uninitialized: reading it unset throws instead of signing against a guessable default. */
    public string $secret;

    /** Token lifetime in seconds (signed into the token, and the cookie's Max-Age). */
    public int $ttl = 3600;

    /** The cookie name. Not HttpOnly on purpose: client-side JS mirrors it into $headerName. */
    public string $cookieName = 'csrf_token';

    /** Request header checked first (the fetch()/XHR path). */
    public string $headerName = 'X-CSRF-Token';

    /** Body field checked second (the plain `<form>` path, see Csrf::field()). */
    public string $fieldName = '_csrf';

    /** Cookie Secure attribute. Null (default): Secure when the request came over HTTPS (behind a trusted proxy, as it says). */
    public ?bool $cookieSecure = null;

    /** Cookie SameSite attribute; 'Lax' still sends it on a top-level navigation into a form. */
    public string $cookieSameSite = 'Lax';

    /**
     * Turns the protection on with a secret kept in $file: read from it, or on the first start 32 random
     * bytes written there as hex, readable by the owner only, so tokens survive a restart without a secret
     * in the environment. A secret set before wins; `$opts->secret = $env->string('CSRF_SECRET', '')` first
     * lets the environment decide in production. Plain file functions: it runs while the app is configured.
     */
    public function keepSecretIn(string $file): void
    {
        if (isset($this->secret) && $this->secret !== '') {
            return;
        }
        $secret = is_file($file) ? trim((string) file_get_contents($file)) : '';
        if ($secret === '') {
            $secret = bin2hex(random_bytes(32));
            if (!is_dir(dirname($file)) && !mkdir(dirname($file), 0700, true) && !is_dir(dirname($file))) {
                throw new RuntimeException("CSRF secret: cannot create the directory of $file");
            }
            if (file_put_contents($file, $secret) === false || !chmod($file, 0600)) {
                throw new RuntimeException("CSRF secret: cannot write $file");
            }
        }
        $this->secret = $secret;
    }
}
