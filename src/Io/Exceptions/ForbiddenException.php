<?php

namespace Io\Exceptions;

use Throwable;
use Io\Enums\Message;

/** HTTP 403 "Forbidden": $message becomes the RFC 9457 'detail', $code overrides the status. */
class ForbiddenException extends HttpException
{
    public function __construct(?string $message = null, int $code = 403, ?Throwable $previous = null)
    {
        parent::__construct(
            statusCode: $code,
            title: Message::Forbidden->value,
            detail: $message,
            previous: $previous
        );
    }
}
