<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent record that stores a single search-provider configuration row.
 *
 * The table name is read from `config('ai-search-providers.table')` at
 * runtime so host applications can keep a legacy table name without
 * subclassing — just override the config key (or extend this model with
 * a hard-coded `protected $table = '...';`).
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $driver
 * @property string|null $base_url
 * @property string|null $api_key_encrypted
 * @property string|null $api_secret_encrypted
 * @property array<string, mixed>|null $config
 * @property int $priority
 * @property int $timeout_seconds
 * @property int|null $rate_limit_per_minute
 * @property bool $is_active
 */
class SearchProviderConfig extends Model
{
    protected $fillable = [
        'code',
        'name',
        'driver',
        'base_url',
        'api_key_encrypted',
        'api_secret_encrypted',
        'config',
        'priority',
        'timeout_seconds',
        'rate_limit_per_minute',
        'is_active',
    ];

    protected $casts = [
        'api_key_encrypted' => 'encrypted',
        'api_secret_encrypted' => 'encrypted',
        'config' => 'array',
        'priority' => 'integer',
        'timeout_seconds' => 'integer',
        'rate_limit_per_minute' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getTable(): string
    {
        if ($this->table !== null) {
            return $this->table;
        }

        if (function_exists('config')) {
            $configured = config('ai-search-providers.table');

            if (is_string($configured) && $configured !== '') {
                return $configured;
            }
        }

        return 'search_providers';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority')->orderBy('id');
    }
}
