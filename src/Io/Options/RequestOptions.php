<?php

namespace Io\Options;

/** Limits on what a request may bring. Configure via App::configure(). */
class RequestOptions
{
    /**
     * A JSON body larger than this (bytes) is answered with 413 instead of decoded. Keys that collide in PHP's
     * array hash make json_decode() slow with the square of their number: 256 KiB of them take about 50 ms, 1 MiB
     * about 750 ms, and the worker serves nothing else meanwhile.
     */
    public int $maxJsonBytes = 262_144;
}
