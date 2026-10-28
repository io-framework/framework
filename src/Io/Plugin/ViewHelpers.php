<?php

namespace Io\Plugin;

use BadMethodCallException;

/**
 * Every plugin's view helpers, merged, as a service: what a view calls as `$this->name()`, for a plugin that
 * renders templates of its own (Live's components) and wants to offer the same.
 */
final readonly class ViewHelpers
{
    /** @param array<string, callable> $helpers name => callable */
    public function __construct(private array $helpers)
    {
    }

    public function has(string $name): bool
    {
        return isset($this->helpers[$name]);
    }

    /** The helper; throws for a name no plugin offers, as a view does */
    public function get(string $name): callable
    {
        return $this->helpers[$name] ?? throw new BadMethodCallException("View helper '{$name}' is not provided by any registered plugin.");
    }

    /** @return array<string, callable> */
    public function all(): array
    {
        return $this->helpers;
    }
}
