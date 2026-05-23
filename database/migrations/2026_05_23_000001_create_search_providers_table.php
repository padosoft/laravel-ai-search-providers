<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = $this->resolveTable();

        if (Schema::hasTable($table)) {
            return;
        }

        Schema::create($table, function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->string('code', 100);
            $blueprint->string('name');
            $blueprint->string('driver', 100);
            $blueprint->text('base_url')->nullable();
            $blueprint->text('api_key_encrypted')->nullable();
            $blueprint->text('api_secret_encrypted')->nullable();
            $blueprint->json('config')->nullable();
            $blueprint->unsignedSmallInteger('priority')->default(100);
            $blueprint->unsignedSmallInteger('timeout_seconds')->default(15);
            $blueprint->unsignedInteger('rate_limit_per_minute')->nullable();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();

            $blueprint->unique('code', 'aisp_code_uq');
            $blueprint->index(['is_active', 'priority'], 'aisp_active_priority_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->resolveTable());
    }

    private function resolveTable(): string
    {
        if (function_exists('config')) {
            $configured = config('ai-search-providers.table');

            if (is_string($configured) && $configured !== '') {
                return $configured;
            }
        }

        return 'search_providers';
    }
};
