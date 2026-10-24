<?php

namespace Io\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Io\App;
use Io\Tests\Fixtures\Support\{FromArrayDTO, RedactableDTO};

final class AppStaticTest extends TestCase
{
    public function testRedactSkipsTheComputedValidationProperties(): void
    {
        $this->assertSame(['name' => 'Ada', 'age' => 0, 'role' => null, 'excluded' => 'default'], App::redact(new FromArrayDTO(['name' => 'Ada'])));
    }

    public function testRedactLeavesOrdinaryPropertiesAsIs(): void
    {
        $dto = new RedactableDTO();
        $dto->username = 'ada';

        $this->assertSame('ada', App::redact($dto)['username']);
    }

    public function testRedactReplacesASensitivePropertyWithItsDefaultPlaceholder(): void
    {
        $dto = new RedactableDTO();
        $dto->password = 'hunter2';

        $this->assertSame('**redacted**', App::redact($dto)['password']);
    }

    public function testRedactUsesASensitivePropertysOwnCustomPlaceholder(): void
    {
        $dto = new RedactableDTO();
        $dto->apiKey = 'sk-real-key';

        $this->assertSame('***', App::redact($dto)['apiKey']);
    }

    public function testRedactReplacesAPiiPropertyWithItsPlaceholder(): void
    {
        $dto = new RedactableDTO();
        $dto->email = 'ada@example.com';

        $this->assertSame('**redacted**', App::redact($dto)['email']);
    }

    public function testRedactSkipsAnUninitializedTypedPropertyRatherThanFatalErroring(): void
    {
        $dto = new RedactableDTO();

        $this->assertArrayNotHasKey('uninitialized', App::redact($dto));
    }

    public function testRedactNeverExposesNonPublicProperties(): void
    {
        $dto = new RedactableDTO();

        $this->assertArrayNotHasKey('internal', App::redact($dto));
    }
}
