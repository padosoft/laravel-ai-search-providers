<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Data;

use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class SearchQueryDataTest extends TestCase
{
    public function test_from_array_normalizes_strings_and_defaults_limit(): void
    {
        $data = SearchQueryData::fromArray([
            'client_id' => 42,
            'brand' => '  Nike ',
            'model' => '',
            'color' => 'White',
            'ean' => null,
            'supplier_sku' => "CW2288-111\n",
            'site' => 'nike.com',
        ]);

        self::assertSame(42, $data->clientId);
        self::assertSame('Nike', $data->brand);
        self::assertNull($data->model);
        self::assertSame('White', $data->color);
        self::assertNull($data->ean);
        self::assertSame('CW2288-111', $data->supplierSku);
        self::assertSame('nike.com', $data->site);
        self::assertSame(10, $data->limit);
    }

    public function test_to_search_string_prefers_explicit_query_when_present(): void
    {
        $data = SearchQueryData::fromArray([
            'brand' => 'Nike',
            'query' => 'Air Force 1 site:nike.com',
        ]);

        self::assertSame('Air Force 1 site:nike.com', $data->toSearchString());
    }

    public function test_to_search_string_composes_brand_model_color_ean_sku_when_query_absent(): void
    {
        $data = SearchQueryData::fromArray([
            'brand' => 'Nike',
            'model' => 'Air Force 1',
            'color' => 'White',
            'ean' => '8050000000000',
            'supplier_sku' => 'CW2288-111',
        ]);

        self::assertSame('Nike Air Force 1 White 8050000000000 CW2288-111', $data->toSearchString());
    }

    public function test_to_search_string_handles_empty_identity(): void
    {
        $data = SearchQueryData::fromArray([]);

        self::assertSame('', $data->toSearchString());
    }

    public function test_limit_is_clamped_to_minimum_one(): void
    {
        $data = SearchQueryData::fromArray(['limit' => 0]);

        self::assertSame(1, $data->limit);
    }

    public function test_with_metadata_merges_deeply_and_returns_new_instance(): void
    {
        $data = SearchQueryData::fromArray([
            'brand' => 'Nike',
            'metadata' => ['attempt' => 1, 'flags' => ['retry' => false]],
        ]);

        $updated = $data->withMetadata(['attempt' => 2, 'flags' => ['retry' => true, 'cooldown' => 30]]);

        self::assertNotSame($data, $updated);
        self::assertSame(['attempt' => 1, 'flags' => ['retry' => false]], $data->metadata);
        self::assertSame(['attempt' => 2, 'flags' => ['retry' => true, 'cooldown' => 30]], $updated->metadata);
    }

    public function test_to_array_round_trip_exposes_search_string(): void
    {
        $data = SearchQueryData::fromArray([
            'brand' => 'Nike',
            'model' => 'Air Force 1',
            'limit' => 5,
        ]);

        $array = $data->toArray();

        self::assertSame('Nike Air Force 1', $array['search_string']);
        self::assertSame(5, $array['limit']);
        self::assertSame('Nike', $array['brand']);
    }
}
