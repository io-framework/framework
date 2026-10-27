<?php

namespace Io\Validation;

/**
 * A check that needs the app, named by #[Check]: a lookup in the database, a call to a service. It comes from
 * the container, so it takes its services through #[Inject].
 */
interface Checker
{
    public function passes(mixed $value, object $dto): bool;
}
