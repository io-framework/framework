<?php

namespace Io\Tests\Integration;

use RuntimeException;
use Io\App;
use Io\Tests\Fixtures\Managers\{MaintenanceManager, CleanupManager};
use Io\Tests\Fixtures\Services\ExampleService;

final class TaskExecutionTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        App::create()->attach([MaintenanceManager::class]);
    }

    public function testExecutesATaskByItsPrefixedFullName(): void
    {
        $result = App::create()->getRouter()->executeTask('maintenance:ping', ['ping'], 1);

        $this->assertSame('pong:1', $result);
    }

    public function testExecutesATaskByItsShortNameWhenUnambiguous(): void
    {
        $result = App::create()->getRouter()->executeTask('ping', ['ping'], 1);

        $this->assertSame('pong:1', $result);
    }

    public function testTaskManagerPropertiesAreInjected(): void
    {
        $result = App::create()->getRouter()->executeTask('maintenance:inject-check');

        $this->assertSame(ExampleService::class, $result);
    }

    public function testUnknownTaskNameThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Task 'does-not-exist' not found.");

        App::create()->getRouter()->executeTask('does-not-exist');
    }

    public function testAmbiguousShortTaskNameAcrossManagersThrows(): void
    {
        App::create()->attach([MaintenanceManager::class, CleanupManager::class]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Ambiguous task name 'ping'.");

        App::create()->getRouter()->executeTask('ping');
    }
}
