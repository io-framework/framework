<?php

namespace Io\Tests\Unit\Http;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Io\Http\{Redirect, Request};

final class RedirectTest extends TestCase
{
    public function testDefaultsTo302(): void
    {
        $redirect = new Redirect('/dashboard');

        $this->assertSame('/dashboard', $redirect->location);
        $this->assertSame(302, $redirect->status);
    }

    public function testAcceptsAnyThreeHundredStatus(): void
    {
        $this->assertSame(301, (new Redirect('/x', 301))->status);
        $this->assertSame(303, (new Redirect('/x', 303))->status);
        $this->assertSame(307, (new Redirect('/x', 307))->status);
        $this->assertSame(308, (new Redirect('/x', 308))->status);
    }

    public function testRejectsANonRedirectStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('3xx');

        new Redirect('/x', 200);
    }

    public function testRejectsAnEmptyLocation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Redirect('');
    }

    public function testRejectsALocationWithALineBreak(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('line breaks');

        new Redirect("/x\r\nSet-Cookie: evil=1");
    }

    public function testBackFollowsALocalPathFromTheQueryOrTheForm(): void
    {
        $query = Request::fromParts('GET', '/login?next=' . rawurlencode('/a/1?tab=cast'));
        $form = Request::fromParts('POST', '/login', form: ['next' => '/profil']);

        $this->assertSame('/a/1?tab=cast', Redirect::back($query)->location);
        $this->assertSame('/profil', Redirect::back($form)->location);
        $this->assertSame(303, Redirect::back($form)->status);
        $this->assertSame('/home', Redirect::back(Request::fromParts('GET', '/login'), '/home')->location);
    }

    public function testBackRefusesEverythingThatLeavesTheSite(): void
    {
        foreach (['https://evil.test/', '//evil.test', '/\\evil.test', '/\\/evil.test', '\\evil.test', 'evil', '/a b', "/a\tb", '', '/%0d%0a'] as $next) {
            $req = Request::fromParts('POST', '/login', form: ['next' => $next]);
            $expected = $next === '/%0d%0a' ? $next : '/fallback';
            $this->assertSame($expected, Redirect::back($req, '/fallback')->location, $next);
        }
    }
}
