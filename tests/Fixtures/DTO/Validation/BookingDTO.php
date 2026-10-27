<?php

namespace Io\Tests\Fixtures\DTO\Validation;

use Io\Attributes\{After, Before, Check, Date, Max, Min, NotBlank};

class BookingDTO
{
    #[NotBlank(
        message: 'Bitte einen Code angeben.'
    )]
    #[Check(
        checker: KnownCode::class,
        message: 'Diesen Code gibt es nicht.'
    )]
    public ?string $code = 'ABC';

    #[Date(
        message: 'Bitte ein gültiges Datum angeben.'
    )]
    #[Before(
        date: 'today'
    )]
    public mixed $birthDate = '1990-05-17';

    #[After(
        date: '2000-01-01'
    )]
    public mixed $start = '2026-01-01';

    #[Min(
        value: 1
    )]
    #[Max(
        value: 9
    )]
    public mixed $seats = 2;
}
