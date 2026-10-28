<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get};

/** Placeholders with patterns: only a path that fits matches */
#[Controller('/patterns')]
final class PatternsController
{
    #[Get('/a/{id:\d+}-{slug:[a-z0-9-]*}')]
    public function anime(int $id, string $slug): array
    {
        return ['id' => $id, 'slug' => $slug];
    }

    #[Get('/year/{year:\d{4}}')]
    public function year(int $year): array
    {
        return ['year' => $year];
    }

    #[Get('/plain/{name}')]
    public function plain(string $name): array
    {
        return ['name' => $name];
    }
}
