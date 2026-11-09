<?php

namespace Io\Http;

use Io\Options\FileSystemOptions;

/**
 * Serves the app's own static files from FileSystemOptions::$publicDirectory, and only from there:
 * a path with a '..' or any other dot-segment, a dotfile or dot-directory (.env, .git/), or a PHP
 * source file is never served, and the resolved file (symlinks followed) must lie inside the
 * directory itself, not merely start with its name, which a sibling like `public-backup` would.
 */
final class PublicFileServer
{
    private const MIME_MAP = [
        'css'  => 'text/css; charset=utf-8',
        'js'   => 'application/javascript; charset=utf-8',
        'mjs'  => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'svg'  => 'image/svg+xml',
        'html' => 'text/html; charset=utf-8',
    ];

    /** From this size on a file is streamed from disk instead of read into memory first (and not compressed). */
    private const STREAM_FROM_BYTES = 1024 * 1024;

    /** Extensions PHP may execute: their source is never handed out, wherever they lie. */
    private const SOURCE_EXTENSION = '/^(php\d*|phtml|phar|phps|pht)$/i';

    public function __construct(private FileSystemOptions $options)
    {
    }

    /** @param Request|null $req Its If-Modified-Since answers 304 for a file not changed since. */
    public function serve(string $path, Response $res, ?Request $req = null): bool
    {
        if ($this->options->publicDirectory === null || !self::isServablePath($path)) {
            return false;
        }
        $root = realpath($this->options->getPublicDirectory());
        $file = $root !== false ? realpath($root . $path) : false;
        if ($root === false || $file === false || !str_starts_with($file, $root . DIRECTORY_SEPARATOR) || !is_file($file)) {
            return false;
        }
        // A symlink inside the directory may point at a dotfile or a PHP file: check what it resolved to as well.
        if (!self::isServablePath(str_replace(DIRECTORY_SEPARATOR, '/', substr($file, strlen($root))))) {
            return false;
        }

        $modified = filemtime($file);
        if ($modified !== false) {
            $res->withHeader('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $modified));
            $since = strtotime($req?->headers['If-Modified-Since'] ?? '');
            if ($since !== false && $since >= $modified) {
                $res->status(304);
                return true;
            }
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $type = self::MIME_MAP[$ext] ?? (mime_content_type($file) ?: 'application/octet-stream');
        if ((int) filesize($file) >= self::STREAM_FROM_BYTES) {
            // A video or a backup: read in pieces while it goes out, never whole into memory
            $res->withHeader('Content-Type', $type)->file($file);
            return true;
        }
        $content = @file_get_contents($file);
        if ($content === false) {
            // @codeCoverageIgnoreStart
            // only a delete/permission race after is_file().
            return false;
            // @codeCoverageIgnoreEnd
        }
        $res->withHeader('Content-Type', $type)
            ->withHeader('Content-Length', (string) strlen($content))
            ->write($content);
        return true;
    }

    /** A '/'-separated path: no segment starting with '.' (so no '..', '.', .env, .git/), no backslash or NUL, no PHP source extension. */
    private static function isServablePath(string $path): bool
    {
        if (str_contains($path, "\0") || str_contains($path, '\\')) {
            return false;
        }
        foreach (explode('/', $path) as $segment) {
            if (str_starts_with($segment, '.')) {
                return false;
            }
        }
        return preg_match(self::SOURCE_EXTENSION, pathinfo($path, PATHINFO_EXTENSION)) !== 1;
    }
}
