<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Data;

use Padosoft\LaravelAiSearchProviders\Data\SearchResult;
use Padosoft\LaravelAiSearchProviders\Data\SearchResultCollection;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class SearchResultCollectionTest extends TestCase
{
    public function test_empty_collection_is_empty_and_iterable(): void
    {
        $collection = new SearchResultCollection();

        self::assertTrue($collection->isEmpty());
        self::assertCount(0, $collection);
        self::assertNull($collection->first());
        self::assertSame([], iterator_to_array($collection));
    }

    public function test_constructor_accepts_arrays_and_search_result_instances(): void
    {
        $collection = new SearchResultCollection([
            ['title' => 'A', 'page_url' => 'https://a.test/a', 'image_url' => null],
            new SearchResult('B', 'https://b.test/b', 'https://b.test/b.jpg'),
        ]);

        self::assertCount(2, $collection);
        self::assertInstanceOf(SearchResult::class, $collection->first());
        self::assertSame('A', $collection->first()?->title);
    }

    public function test_add_is_immutable_and_returns_new_collection(): void
    {
        $collection = new SearchResultCollection();
        $extra = new SearchResult('A', 'https://a.test/a', null);

        $updated = $collection->add($extra);

        self::assertCount(0, $collection);
        self::assertCount(1, $updated);
    }

    public function test_to_array_round_trip(): void
    {
        $collection = new SearchResultCollection([
            new SearchResult('A', 'https://a.test/a', 'https://a.test/a.jpg'),
        ]);

        $array = $collection->toArray();

        self::assertCount(1, $array);
        self::assertSame('A', $array[0]['title']);
        self::assertArrayHasKey('fingerprint', $array[0]);
    }
}
