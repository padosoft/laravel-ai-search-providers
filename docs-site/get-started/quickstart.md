---
title: Quickstart
description: Install the package and run a deterministic fake search in a Laravel app.
---

# Quickstart

This path gets a working search result without API keys.

::: steps
1. **Install the package**

   ```bash
   composer require padosoft/laravel-ai-search-providers
   ```

2. **Run the migration**

   ```bash
   php artisan migrate
   ```

3. **Insert a fake provider**

   ```php
   \Padosoft\LaravelAiSearchProviders\Models\SearchProviderConfig::query()->create([
       'code' => 'quickstart-fake',
       'name' => 'Quickstart Fake',
       'driver' => 'fake',
       'config' => [
           'image_results' => [[
               'title' => 'Quick Start Demo',
               'page_url' => 'https://example.test/p/demo',
               'image_url' => 'https://cdn.example.test/demo.jpg',
               'source_domain' => 'example.test',
               'width' => 1200,
               'height' => 1200,
           ]],
       ],
       'priority' => 1,
       'timeout_seconds' => 5,
       'is_active' => true,
   ]);
   ```

4. **Run a search**

   ```php
   use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
   use Padosoft\LaravelAiSearchProviders\SearchProviderManager;

   $execution = app(SearchProviderManager::class)->searchImages(SearchQueryData::fromArray([
       'brand' => 'Nike',
       'model' => 'Air Force 1 07',
       'color' => 'White',
       'site' => 'nike.com',
       'limit' => 5,
   ]));

   dump($execution->provider?->code);
   dump($execution->results->first()?->title);
   ```
:::

::: callout success "Expected result"
The provider code is `quickstart-fake`, and the first result title is `Quick Start Demo`.
:::

