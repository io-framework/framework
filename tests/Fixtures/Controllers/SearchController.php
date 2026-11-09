<?php

namespace Io\Tests\Fixtures\Controllers;

use Io\Attributes\{Controller, Get, Post, Version};

/** One action under two route attributes: a search by link and by form. */
#[Controller('/search')]
#[Version('v1')]
class SearchController
{
    #[Get]
    #[Post]
    public function search(): array
    {
        return ['found' => true];
    }
}
