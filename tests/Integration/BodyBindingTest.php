<?php

namespace Io\Tests\Integration;

use Io\App;
use Io\Http\Request;
use Io\Options\{RendererOptions, RequestOptions};
use Io\Tests\Fixtures\Controllers\BodyBindingController;

final class BodyBindingTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        App::create()->attach([BodyBindingController::class]);
    }

    public function testAPropertyWithoutADefaultIsRequired(): void
    {
        $res = $this->request('POST', '/body/required')->withJson(['qty' => 2])->send();

        $this->assertSame(422, $res->status());
        $this->assertSame(['name' => ['This value is required.']], $res->json()['errors'] ?? null);

        $this->request('POST', '/body/required')->withJson(['name' => 'Ada'])->send()->assertOk();
    }

    public function testAnArrayParameterGetsTheDecodedBody(): void
    {
        $this->request('POST', '/body/raw')->withJson(['a' => 1, 'b' => [2]])->send()->assertOk()->assertJson(['a' => 1, 'b' => [2]]);
    }

    public function testEnumsAndNestedDtosAreBuiltFromTheBody(): void
    {
        $this->request('POST', '/body/shipment')->withJson(['status' => 'done', 'to' => ['city' => 'Freiburg'], 'stops' => [['city' => 'Basel']]])->send()
            ->assertOk()->assertJson(['status' => 'done', 'to' => 'Freiburg', 'stops' => 1]);

        $res = $this->request('POST', '/body/shipment')->withJson(['status' => 'lost', 'to' => ['city' => ''], 'stops' => [['city' => '']]])->send();
        $this->assertSame(422, $res->status());
        $this->assertSame(['status', 'to.city', 'stops.0.city'], array_keys($res->json()['errors'] ?? []));
    }

    public function testWithoutValidationTheMethodGetsTheInvalidDtoAndAViewAnswers422(): void
    {
        App::create()->configure(fn (RendererOptions $views) => $views->directory = __DIR__ . '/../Fixtures/Views');

        $res = $this->request('POST', '/body/form')->withBody(['qty' => '2'])->send();
        $this->assertSame(422, $res->status());
        $res->assertContentType('text/html');

        $this->request('POST', '/body/form')->withBody(['name' => 'Ada'])->send()->assertOk()->assertJson(['name' => 'Ada']);
    }

    public function testAJsonBodyOverTheLimitIsTooLargeAndNeverDecoded(): void
    {
        $app = App::create();
        $app->configure(fn (RequestOptions $requests) => $requests->maxJsonBytes = 20);
        $json = ['Content-Type' => 'application/json'];

        $res = $app->handle(Request::fromParts('POST', '/body/raw', $json, '{"name":"a long enough name"}'));
        $this->assertSame(413, $res->getStatus());
        $this->assertSame('Content Too Large', json_decode($res->getBody(), true)['title'] ?? null);

        $this->assertSame(200, $app->handle(Request::fromParts('POST', '/body/raw', $json, '{"name":"Ada"}'))->getStatus());
    }

    public function testAJsonBodyIsDecodedOnlyWhenARouteReadsIt(): void
    {
        $req = Request::fromParts('POST', '/nowhere', ['Content-Type' => 'application/json'], '{"name":"Ada"}');
        $pending = new \ReflectionProperty(Request::class, 'json');

        $this->assertSame(404, App::create()->handle($req)->getStatus());
        $this->assertSame('{"name":"Ada"}', $pending->getValue($req));
        $this->assertSame(['name' => 'Ada'], $req->body);
        $this->assertNull($pending->getValue($req));
    }

    public function testAMalformedJsonBodyIsABadRequest(): void
    {
        $res = App::create()->handle(Request::fromParts('POST', '/body/required', ['Content-Type' => 'application/json'], '{"name":"a",'));

        $this->assertSame(400, $res->getStatus());
        $this->assertSame('Bad Request', json_decode($res->getBody(), true)['title'] ?? null);
    }
}
