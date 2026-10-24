<?php

namespace Io\Attributes;

use Attribute;

/** Routes a controller method for HTTP PUT. */
#[Attribute(Attribute::TARGET_METHOD)]
class Put extends HttpRoute
{
    public function getHttpMethod(): string
    {
        return 'PUT';
    }
}
