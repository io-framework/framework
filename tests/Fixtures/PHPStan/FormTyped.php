<?php

namespace Io\Tests\Fixtures\PHPStan;

use Io\Attributes\Body;
use Io\Form;
use Io\Tests\Fixtures\DTO\PlainSignup;

final class FormTyped
{
    public function signup(#[Body(of: PlainSignup::class)] Form $form): string
    {
        return $form->dto->email;
    }
}
