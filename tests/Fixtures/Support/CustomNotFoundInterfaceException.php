<?php

namespace Io\Tests\Fixtures\Support;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Implements the same interface Io\Exceptions\NotFoundException does,
 * without extending it or being separately registered. Used to prove
 * App::resolveExceptionHandler() falls back to matching by interface.
 */
class CustomNotFoundInterfaceException extends Exception implements NotFoundExceptionInterface
{
}
