<?php

namespace Io\Tests\Unit;

use Io\App;
use Io\Tests\Fixtures\DTO\Validation\BookingDTO;
use Io\Validation\Messages;
use Io\Validator;
use PHPUnit\Framework\TestCase;

final class ValidationRulesTest extends TestCase
{
    protected function tearDown(): void
    {
        App::reset();
    }

    public function testAValidDtoHasNoErrors(): void
    {
        $this->assertSame([], (new Validator())->validate(new BookingDTO()));
    }

    public function testDateRejectsWhatIsNoRealDateInItsFormat(): void
    {
        foreach (['2026-02-30', '17.05.1990', 'yesterday', 42] as $value) {
            $dto = new BookingDTO();
            $dto->birthDate = $value;
            $this->assertSame(['Bitte ein gültiges Datum angeben.'], (new Validator())->validate($dto)['birthDate'] ?? null, var_export($value, true));
        }
    }

    public function testDateAcceptsADateTimeObjectAndNull(): void
    {
        foreach ([new \DateTimeImmutable('1990-05-17'), null] as $value) {
            $dto = new BookingDTO();
            $dto->birthDate = $value;
            $this->assertArrayNotHasKey('birthDate', (new Validator())->validate($dto));
        }
    }

    public function testBeforeAndAfterCompareWithTheirDate(): void
    {
        $dto = new BookingDTO();
        $dto->birthDate = date('Y-m-d');
        $dto->start = '1999-12-31';

        $errors = (new Validator())->validate($dto);

        $this->assertSame(['This value should be before today.'], $errors['birthDate']);
        $this->assertSame(['This value should be after 2000-01-01.'], $errors['start']);
    }

    public function testBeforeLeavesWhatIsNoDateToDate(): void
    {
        $dto = new BookingDTO();
        $dto->birthDate = 'soon';

        // Only #[Date] speaks: "before today" would say nothing new
        $this->assertSame(['Bitte ein gültiges Datum angeben.'], (new Validator())->validate($dto)['birthDate']);
    }

    public function testMinAndMaxBoundANumberAndLeaveOtherTypesAlone(): void
    {
        $dto = new BookingDTO();
        $dto->seats = 0;
        $this->assertSame(['This value should be 1 or more.'], (new Validator())->validate($dto)['seats']);

        $dto->seats = 9.5;
        $this->assertSame(['This value should be 9 or less.'], (new Validator())->validate($dto)['seats']);

        $dto->seats = 'many';
        $this->assertArrayNotHasKey('seats', (new Validator())->validate($dto));
    }

    public function testCheckAsksTheCheckerAndEveryFailingRuleSpeaks(): void
    {
        $dto = new BookingDTO();
        $dto->code = 'NOPE';
        $this->assertSame(['Diesen Code gibt es nicht.'], (new Validator())->validate($dto)['code']);

        $dto->code = '   ';
        $this->assertSame(
            ['Bitte einen Code angeben.', 'Diesen Code gibt es nicht.'],
            (new Validator())->validate($dto)['code'],
        );
    }

    public function testAnOwnMessageGoesThroughTheBoundMessagesLikeTheEnglishOnes(): void
    {
        App::create()->getContainer()->bind(Messages::class, new class implements Messages {
            public function translate(string $text, array $params): string
            {
                return "[$text]";
            }
        });
        $dto = new BookingDTO();
        $dto->seats = 0;
        $dto->code = '';

        $errors = (new Validator())->validate($dto);

        $this->assertSame(['[This value should be {min} or more.]'], $errors['seats']);
        $this->assertSame('[Bitte einen Code angeben.]', $errors['code'][0]);
    }
}
