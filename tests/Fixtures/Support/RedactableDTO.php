<?php

namespace Io\Tests\Fixtures\Support;

use Io\Attributes\{Sensitive, PII};

/** Plain object (not FromArray) exercising App::redact() (see IoTest). */
class RedactableDTO
{
    public string $username = '';

    #[Sensitive]
    public string $password = '';

    #[Sensitive(placeholder: '***')]
    public string $apiKey = '';

    #[PII]
    public string $email = '';

    /** Never assigned: exercises redact()'s isInitialized() guard. */
    public string $uninitialized;

    private string $internal = 'never exposed';
}
