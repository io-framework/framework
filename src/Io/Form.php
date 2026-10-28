<?php

namespace Io;

/**
 * A DTO built from a request with what was wrong with it: what `#[Body(of: LoginForm::class)] Form $form`
 * hands a method that shows a form again. The DTO stays a plain data object; the errors live here.
 *
 * ```php
 * if (!$form->valid) {
 *     return new View('Account/Login', $form);
 * }
 * $form->fail('password', 'Email or password do not match.');
 * ```
 *
 * @template T of object
 */
final class Form
{
    /** @var array<string, list<string>>|null computed on the first read, again after fail() */
    private ?array $computed = null;

    /**
     * field => messages: the rules first, then values that did not fit, required fields left out, nested DTOs'
     * errors ("address.city") and fail() entries.
     *
     * @var array<string, list<string>>
     */
    public array $errors {
        get => $this->computed ??= Validator::merge(Hydrator::errorsOf($this->dto, $this->failures), $this->added);
    }

    public bool $valid {
        get => $this->errors === [];
    }

    /** @var array<string, list<string>> */
    private array $added = [];

    /**
     * @param T $dto
     * @param array<string, list<string>> $failures What the hydrator could not fit
     */
    public function __construct(public readonly object $dto, private readonly array $failures = [])
    {
    }

    /** Records a failure the rules cannot know about (a taken email); it joins the field's other messages. */
    public function fail(string $field, string $message): static
    {
        $this->added[$field][] = $message;
        $this->computed = null;
        return $this;
    }
}
