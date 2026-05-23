<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Contracts;

/**
 * Generic search-event logging contract used by SearchProviderManager.
 *
 * The manager calls this hook on successful, failed, skipped and empty
 * provider attempts. Implementations decide whether to write to a database
 * audit table, push to a queue, or merely log to the Laravel log channel.
 *
 * Return type is intentionally `mixed` so existing host-application loggers
 * (e.g. ones that return a structured event row for downstream audit
 * storage) satisfy the contract without forcing a void return.
 */
interface SearchEventLoggerInterface
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function record(string $eventType, array $context = [], string $level = 'info'): mixed;
}
