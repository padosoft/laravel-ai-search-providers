---
title: Contracts
description: Interfaces for providers, repositories, factories, and logging.
---

# Contracts

## `SearchProviderInterface`

Drivers implement search methods and support flags.

## `SearchProviderFactoryInterface`

Factories create providers from `SearchProviderDefinition`.

## `SearchProviderConfigRepositoryInterface`

Repositories return active definitions. The default implementation reads Eloquent rows.

## `SearchEventLoggerInterface`

Receives provider attempt events for audit and observability.

