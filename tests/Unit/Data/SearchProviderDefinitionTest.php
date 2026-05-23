<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Data;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class SearchProviderDefinitionTest extends TestCase
{
    public function test_from_array_applies_defaults_and_normalizes_strings(): void
    {
        $definition = SearchProviderDefinition::fromArray([
            'code' => 'brave',
            'driver' => 'brave',
            'base_url' => '  https://api.search.brave.com  ',
            'api_key' => ' secret ',
            'config' => ['supports_image_search' => true],
            'timeout_seconds' => 0,
        ]);

        self::assertSame('brave', $definition->code);
        self::assertSame('brave', $definition->driver);
        self::assertSame('https://api.search.brave.com', $definition->baseUrl);
        self::assertSame('secret', $definition->apiKey);
        self::assertSame(100, $definition->priority);
        self::assertSame(1, $definition->timeoutSeconds);
        self::assertTrue($definition->isActive);
    }

    public function test_name_falls_back_to_code(): void
    {
        $definition = SearchProviderDefinition::fromArray([
            'code' => 'tavily',
            'driver' => 'tavily',
        ]);

        self::assertSame('tavily', $definition->name);
    }

    public function test_config_value_returns_default_when_key_missing(): void
    {
        $definition = SearchProviderDefinition::fromArray([
            'code' => 'exa',
            'driver' => 'exa',
            'config' => ['image_links_per_result' => 7],
        ]);

        self::assertSame(7, $definition->configValue('image_links_per_result'));
        self::assertSame('auto', $definition->configValue('search_type', 'auto'));
    }

    public function test_to_safe_array_never_leaks_api_secrets(): void
    {
        $definition = SearchProviderDefinition::fromArray([
            'code' => 'firecrawl',
            'driver' => 'firecrawl',
            'api_key' => 'fc-secret',
            'api_secret' => 'extra-secret',
        ]);

        $safe = $definition->toSafeArray();

        self::assertArrayNotHasKey('api_key', $safe);
        self::assertArrayNotHasKey('api_secret', $safe);
        self::assertTrue($safe['has_api_key']);
        self::assertTrue($safe['has_api_secret']);
    }
}
