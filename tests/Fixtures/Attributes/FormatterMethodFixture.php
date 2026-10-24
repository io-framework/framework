<?php

namespace Io\Tests\Fixtures\Attributes;

use Io\Attributes\{FileFormatter, JSONFormatter, SimpleXmlFormatter};

class FormatterMethodFixture
{
    #[FileFormatter('text/plain', 'foo.txt')]
    public function file() {}

    #[JSONFormatter]
    public function json() {}

    #[SimpleXmlFormatter]
    public function xml() {}
}
