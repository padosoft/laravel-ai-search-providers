<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Data;

use Padosoft\LaravelAiSearchProviders\Data\SearchResult;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class SearchResultTest extends TestCase
{
    public function test_from_array_normalizes_strings_and_numeric_casts(): void
    {
        $result = SearchResult::fromArray([
            'title' => 'Nike AF1',
            'page_url' => '  https://nike.com/af1 ',
            'image_url' => 'https://cdn.nike.com/af1.jpg',
            'thumbnail_url' => '',
            'source_domain' => '  cdn.nike.com ',
            'snippet' => null,
            'width' => '1200',
            'height' => '900',
            'score' => '0.81',
            'provider_metadata' => ['provider' => 'brave'],
        ]);

        self::assertSame('Nike AF1', $result->title);
        self::assertSame('https://nike.com/af1', $result->pageUrl);
        self::assertSame('https://cdn.nike.com/af1.jpg', $result->imageUrl);
        self::assertNull($result->thumbnailUrl);
        self::assertSame('cdn.nike.com', $result->sourceDomain);
        self::assertNull($result->snippet);
        self::assertSame(1200, $result->width);
        self::assertSame(900, $result->height);
        self::assertSame(0.81, $result->score);
        self::assertSame(['provider' => 'brave'], $result->providerMetadata);
    }

    public function test_fingerprint_is_deterministic_for_same_page_and_image(): void
    {
        $a = SearchResult::fromArray([
            'title' => 'A',
            'page_url' => 'https://Nike.com/AF1',
            'image_url' => 'HTTPS://cdn.nike.com/Image.jpg',
        ]);

        $b = SearchResult::fromArray([
            'title' => 'B',
            'page_url' => 'https://nike.com/af1',
            'image_url' => 'https://cdn.nike.com/image.jpg',
        ]);

        self::assertSame($a->fingerprint(), $b->fingerprint());
    }

    public function test_to_array_round_trip_includes_fingerprint_and_preserves_metadata(): void
    {
        $payload = [
            'title' => 'Nike AF1',
            'page_url' => 'https://nike.com/af1',
            'image_url' => 'https://cdn.nike.com/af1.jpg',
            'thumbnail_url' => null,
            'source_domain' => 'nike.com',
            'snippet' => 'Iconic AF1.',
            'width' => 1200,
            'height' => 1200,
            'score' => 0.9,
            'provider_metadata' => ['provider' => 'tavily', 'image_description' => 'White AF1'],
        ];

        $array = SearchResult::fromArray($payload)->toArray();

        self::assertArrayHasKey('fingerprint', $array);
        self::assertNotEmpty($array['fingerprint']);
        self::assertSame($payload['title'], $array['title']);
        self::assertSame($payload['provider_metadata'], $array['provider_metadata']);
    }
}
