<?php

namespace Io\Tests\Integration;

use Io\App;
use Io\Options\RendererOptions;
use Io\Tests\Fixtures\Controllers\FormsController;

/** #[Body(of: Dto::class)] Form $form and plain DTOs built by the Hydrator */
final class FormBindingTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = App::create();
        $app->configure(function (RendererOptions $opts) {
            $opts->directory = __DIR__ . '/../Fixtures/Views';
        });
        $app->attach([FormsController::class]);
    }

    public function testAFormReachesTheMethodWithItsErrorsInsteadOfA422(): void
    {
        $output = json_decode($this->dispatch('POST', '/forms/signup', [], ['email' => ' ada@example.test ', 'username' => 'a', 'password' => 'secret12']), true);

        $this->assertSame(200, $this->lastResponse?->status());
        $this->assertFalse($output['valid']);
        $this->assertSame(['username'], array_keys($output['errors']));
        $this->assertSame('ada@example.test', $output['email']);
    }

    public function testFailInTheMethodMakesTheFormInvalid(): void
    {
        $output = json_decode($this->dispatch('POST', '/forms/signup', [], ['email' => 'a@b.test', 'username' => 'taken', 'password' => 'secret12']), true);

        $this->assertSame(['username' => ['Someone has it.']], $output['errors']);
    }

    public function testAPageShowingAnInvalidFormAnswers422(): void
    {
        $output = $this->dispatch('POST', '/forms/page', [], ['email' => 'nope']);

        $this->assertSame(422, $this->lastResponse?->status());
        $this->assertStringContainsString('errors for nope', $output);
    }

    public function testAPlainReadonlyDtoIsBuiltAndValidated(): void
    {
        $this->assertSame(['product' => 'pretzel', 'quantity' => 2], json_decode($this->dispatch('POST', '/forms/line', [], ['product' => ' pretzel ', 'quantity' => 2]), true));

        $this->dispatch('POST', '/forms/line', [], ['quantity' => 0]);
        $this->assertSame(422, $this->lastResponse?->status());
    }

    public function testTheApiSpecDescribesAFormBodyAsItsDto(): void
    {
        $spec = (new \Io\OpenAPI\OpenAPIGenerator(App::create()->getRouter()))->generate();

        $this->assertSame(['$ref' => '#/components/schemas/PlainSignup'], $spec['paths']['/forms/signup']['post']['requestBody']['content']['application/json']['schema'] ?? null);
    }
}
