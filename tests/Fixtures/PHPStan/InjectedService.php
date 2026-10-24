<?php

namespace Io\Tests\Fixtures\PHPStan;

use Io\Attributes\Inject;
use Io\Environment;

/** Analysed by InjectedPropertiesTest: the property is only read here, the container writes it */
final class InjectedService
{
    #[Inject]
    private Environment $env;

    public function port(): mixed
    {
        return $this->env->get('PORT');
    }
}
