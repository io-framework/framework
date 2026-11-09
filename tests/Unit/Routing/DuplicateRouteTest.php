<?php

namespace Io\Tests\Unit\Routing;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Io\RouteCompiler;
use Io\Tests\Fixtures\Controllers\DuplicateRoutes\{
    ConstrainedPlaceholderController,
    DynamicDuplicateAgainController,
    DynamicDuplicateController,
    RenamedPlaceholderController,
    StaticDuplicateController,
};

final class DuplicateRouteTest extends TestCase
{
    public function testTheSameMethodAndPathTwiceFailsTheCompile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Duplicate route GET /dup/x: ' . StaticDuplicateController::class . '::one() and ' . StaticDuplicateController::class . '::two().');
        (new RouteCompiler())->compile([StaticDuplicateController::class]);
    }

    public function testTheSameDynamicPatternTwiceFailsTheCompile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Duplicate route GET /dup/{id}');
        (new RouteCompiler())->compile([DynamicDuplicateController::class, DynamicDuplicateAgainController::class]);
    }

    public function testOnlyAnotherPlaceholderNameIsStillADuplicate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Duplicate route GET /dup/{slug}');
        (new RouteCompiler())->compile([DynamicDuplicateController::class, RenamedPlaceholderController::class]);
    }

    public function testAnotherPlaceholderPatternIsNoDuplicate(): void
    {
        $plans = (new RouteCompiler())->compile([DynamicDuplicateController::class, ConstrainedPlaceholderController::class]);
        $this->assertCount(2, $plans['dynamicRoutes']['GET']);
    }

    public function testTheSamePathUnderAnotherMethodIsNoDuplicate(): void
    {
        $plans = (new RouteCompiler())->compile([DynamicDuplicateAgainController::class]);
        $this->assertCount(1, $plans['dynamicRoutes']['POST']);
    }
}
