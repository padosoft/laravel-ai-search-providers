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
```

## `SearchQueryData`

Fields: `clientId`, `brand`, `model`, `color`, `ean`, `supplierSku`, `query`, `site`, `limit`, `metadata`.

## `SearchProviderExecutionResult`

Fields: `provider`, `results`, `attempts`, `usedFallback`.

