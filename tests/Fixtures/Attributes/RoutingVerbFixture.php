<?php

namespace Io\Tests\Fixtures\Attributes;

use Io\Attributes\{Get, Post, Put, Patch, Delete, Task};

class RoutingVerbFixture
{
    #[Get('/users/{id}')]
    public function get() {}
    #[Post('/users')]
    public function post() {}
    #[Put('/users/{id}')]
    public function put() {}
    #[Patch('/users/{id}')]
    public function patch() {}
    #[Delete('/users/{id}')]
    public function delete() {}
    #[Task('cleanup')]
    public function cleanup() {}
    #[Task('/padded/')]
    public function padded() {}
    #[Task]
    public function unnamed() {}
}
