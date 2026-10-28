<?php

namespace Io\Tests\Unit;

use Io\{Form, Hydrator};
use Io\Tests\Fixtures\DTO\{PlainAddress, PlainSignup, PromotedLine};
use PHPUnit\Framework\TestCase;

final class HydratorTest extends TestCase
{
    public function testAPlainDtoIsFilledTrimmedAndValidatedInItsForm(): void
    {
        $form = Hydrator::form(PlainSignup::class, ['email' => '  ada@example.test ', 'username' => '  ab ', 'password' => ' secret12 ', 'age' => '41', 'unknown' => 1]);

        $this->assertSame('ada@example.test', $form->dto->email);
        $this->assertSame('ab', $form->dto->username);
        $this->assertSame(' secret12 ', $form->dto->password);
        $this->assertSame(41, $form->dto->age);
        $this->assertFalse($form->valid);
        $this->assertSame(['username'], array_keys($form->errors));
    }

    public function testAValueThatDoesNotFitIsReportedNotCast(): void
    {
        $form = Hydrator::form(PlainSignup::class, ['email' => 'a@b.test', 'username' => 'ada', 'password' => 'secret12', 'age' => 'old']);

        $this->assertSame(0, $form->dto->age);
        $this->assertSame(['age' => ['Expected a value of type int.']], $form->errors);
    }

    public function testANestedPlainDtoIsBuiltAndItsErrorsArePrefixed(): void
    {
        $form = Hydrator::form(PlainSignup::class, ['email' => 'a@b.test', 'username' => 'ada', 'password' => 'secret12', 'address' => ['city' => '']]);

        $this->assertInstanceOf(PlainAddress::class, $form->dto->address);
        $this->assertSame(['address.city' => ['This value should not be blank.']], $form->errors);
    }

    public function testFailAddsToTheErrorsAfterTheyWereRead(): void
    {
        $form = Hydrator::form(PlainSignup::class, ['email' => 'a@b.test', 'username' => 'ada', 'password' => 'secret12']);
        $this->assertTrue($form->valid);

        $form->fail('email', 'Taken.');

        $this->assertFalse($form->valid);
        $this->assertSame(['email' => ['Taken.']], $form->errors);
    }

    public function testAReadonlyDtoGetsItsConstructorParametersByName(): void
    {
        $form = Hydrator::form(PromotedLine::class, ['product' => ' pretzel ', 'quantity' => '3']);

        $this->assertInstanceOf(PromotedLine::class, $form->dto);
        $this->assertSame('pretzel', $form->dto->product);
        $this->assertSame(3, $form->dto->quantity);
        $this->assertNull($form->dto->note);
        $this->assertTrue($form->valid);
    }

    public function testAReadonlyDtoMissingARequiredParameterStillComesBackForTheForm(): void
    {
        $form = Hydrator::form(PromotedLine::class, ['quantity' => '0']);

        $this->assertInstanceOf(PromotedLine::class, $form->dto);
        $this->assertSame(0, $form->dto->quantity);
        $this->assertSame(['This value is required.'], $form->errors['product'] ?? null);
        $this->assertArrayHasKey('quantity', $form->errors);
    }

    public function testTheFormIsGenericOverItsDto(): void
    {
        $this->assertInstanceOf(Form::class, Hydrator::form(PlainAddress::class, []));
    }
}
