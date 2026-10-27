<?php

namespace Io\Attributes;

use Attribute;
use Io\App;
use Io\Validation\{Checker, Rule};

/**
 * Validation: the Checker $checker must pass the value, for what needs the app (a code that has to exist in the
 * database). The checker comes from the container, so it takes its services through #[Inject].
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Check implements Rule
{
    /** @param class-string<Checker> $checker */
    public function __construct(
        public string $checker,
        public ?string $message = null,
    ) {
    }

    public function passes(mixed $value, object $dto): bool
    {
        // PHPStan checks the class-string<Checker> where the attribute is written
        $checker = App::getInstance()?->getContainer()->get($this->checker) ?? new $this->checker();

        return $checker->passes($value, $dto);
    }

    public function message(mixed $value): string
    {
        return $this->message ?? 'This value is not valid.';
    }

    public function params(): array
    {
        return [];
    }
}
