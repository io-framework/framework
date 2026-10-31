<?php

namespace Io;

/**
 * Sign-ins that ended before their time, from whoever ends them (a session store, at a sign-out or a new password)
 * to whoever keeps a principal longer than one request (a WebSocket). A principal names its sign-in in the claim
 * CLAIM; a JWT without one can't be ended early. Under Tenma it reaches every worker through Messaging\Channel,
 * and with `tenma --pubsub` or Channel::connect() every process on that Redis; outside Tenma (tests) the listeners
 * of this process.
 */
final class SignOuts
{
    /** The claim of $req->principal that names its sign-in. */
    public const string CLAIM = 'sid';

    private const string CHANNEL = 'io-sign-outs';

    /** @var list<callable(list<string>): void> Outside Tenma: who listens in this process */
    private static array $listeners = [];

    /**
     * Tells every listener that the sign-ins $ids ended.
     *
     * @param list<string> $ids
     */
    public static function publish(array $ids): void
    {
        if ($ids === []) {
            return;
        }
        if (self::acrossWorkers()) {
            new \Messaging\Channel(self::CHANNEL)->publish(implode("\n", $ids));
            return;
        }
        foreach (self::$listeners as $listener) {
            $listener($ids);
        }
    }

    /**
     * Calls $ended with the ids of every sign-in that ends from now on, in this worker. Detached: it belongs to no
     * request and lasts as long as the worker. Like any subscription it keeps a script without a server running:
     * listen once the first long-lived connection needs it, not at boot.
     *
     * @param callable(list<string>): void $ended
     */
    public static function listen(callable $ended): void
    {
        if (!self::acrossWorkers()) {
            self::$listeners[] = $ended;
            return;
        }
        \Threading\Task::detach(static function () use ($ended): void {
            new \Messaging\Channel(self::CHANNEL)->subscribe(
                static fn (string $message) => $ended(explode("\n", $message)),
            );
        });
    }

    /** @internal For tests: forgets the listeners of this process. */
    public static function reset(): void
    {
        self::$listeners = [];
    }

    private static function acrossWorkers(): bool
    {
        // Tenma's own function behind Messaging\Channel
        return function_exists('tenma_pubsub');
    }
}
