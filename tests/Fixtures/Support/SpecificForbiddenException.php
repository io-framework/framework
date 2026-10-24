<?php

namespace Io\Tests\Fixtures\Support;

use Io\Exceptions\ForbiddenException;

/**
 * A subclass of a registered exception type, never registered itself.
 * Used to prove App::resolveExceptionHandler() falls back to matching by
 * parent class when there's no exact match.
 */
class SpecificForbiddenException extends ForbiddenException
{
}
