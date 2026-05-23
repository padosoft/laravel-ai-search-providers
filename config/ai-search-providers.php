<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Backing table
    |--------------------------------------------------------------------------
    |
    | Name of the database table that stores provider configuration rows.
    | The package's `Models\SearchProviderConfig` model uses this value at
    | runtime so host applications can keep an existing legacy table without
    | renaming. The bundled migration is hasTable()-guarded so it skips when
    | the table already exists.
    */
    'table' => env('AI_SEARCH_PROVIDERS_TABLE', 'search_providers'),

    /*
    |--------------------------------------------------------------------------
    | Backing Eloquent model
    |--------------------------------------------------------------------------
    |
    | Optional override for the Eloquent model class the
    | EloquentSearchProviderConfigRepository should use. Leave null to use
    | the bundled `Padosoft\LaravelAiSearchProviders\Models\SearchProviderConfig`.
    | Host applications can set this to a local subclass when they need to
    | preserve a custom table name or extend the model with extra columns.
    */
    'model' => null,

    /*
    |--------------------------------------------------------------------------
    | Auto-load migrations
    |--------------------------------------------------------------------------
    |
    | When true (default), the package calls loadMigrationsFrom() so the
    | create-table migration runs out of the box on `php artisan migrate`.
    | Set to false when you prefer the publish-then-migrate flow for full
    | control over the schema file.
    */
    'load_migrations' => env('AI_SEARCH_PROVIDERS_LOAD_MIGRATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Provider factories override
    |--------------------------------------------------------------------------
    |
    | Map of driver name => factory definition. When set, these factories are
    | merged on top of the package defaults registered by the service provider.
    | Use this to register custom drivers or replace a built-in factory with
    | a wrapper (e.g. for caching, rate-limit enforcement, or test stubs).
    |
    | Each value must be either:
    |   - a Closure(SearchProviderDefinition): SearchProviderInterface, or
    |   - a fully-qualified class implementing SearchProviderFactoryInterface.
    */
    'factories' => [
        // 'my-driver' => static fn ($definition) => new MySearchProvider($definition),
    ],
];
