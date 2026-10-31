<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Body, Controller, Get, Post};
use Io\Form;
use Io\Http\View;
use Io\Tests\Fixtures\DTO\{PlainSignup, PromotedLine};

/** Forms as a page takes them: the DTO with its errors, never a 422 before the method */
#[Controller('/forms')]
final class FormsController
{
    #[Post('/signup')]
    public function signup(#[Body(of: PlainSignup::class)] Form $form): array
    {
        if ($form->valid && $form->dto->username === 'taken') {
            $form->fail('username', 'Someone has it.');
        }
        return ['valid' => $form->valid, 'errors' => $form->errors, 'email' => $form->dto->email];
    }

    #[Post('/page')]
    public function page(#[Body(of: PlainSignup::class)] Form $form): View
    {
        return new View('FormPage', $form, partial: true);
    }

    /** The empty page: a GET fills nothing, so its form shows no errors */
    #[Get('/page')]
    public function emptyPage(#[Body(of: PlainSignup::class)] Form $form): View
    {
        return new View('FormPage', $form, partial: true);
    }

    /** A plain DTO without Form: invalid answers 422 as with FromArray */
    #[Post('/line')]
    public function line(#[Body] PromotedLine $line): array
    {
        return ['product' => $line->product, 'quantity' => $line->quantity];
    }
}
