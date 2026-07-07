---
title: API
description: Runtime PHP API reference.
---

# API

## `SearchProviderManager`

```php
public function searchImages(SearchQueryData $query): SearchProviderExecutionResult;
public function searchWeb(SearchQueryData $query): SearchProviderExecutionResult;
public function registerFactory(string $driver, SearchProviderFactoryInterface $factory): self;
public function drivers(): array;
public function hasDriver(string $driver): bool;
```

`drivers()` returns the registered driver names; `hasDriver()` checks a single driver. Use them to validate provider configurations against the real driver registry instead of hardcoding driver lists.

## `SearchQueryData`

Fields: `clientId`, `brand`, `model`, `color`, `ean`, `supplierSku`, `query`, `site`, `limit`, `metadata`.

## `SearchProviderExecutionResult`

Fields: `provider`, `results`, `attempts`, `usedFallback`.

