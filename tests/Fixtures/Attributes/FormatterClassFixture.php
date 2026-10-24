<?php

namespace Io\Tests\Fixtures\Attributes;

use Io\Attributes\FileFormatter;

#[FileFormatter('application/pdf', 'report.pdf', true)]
class FormatterClassFixture
{
}
