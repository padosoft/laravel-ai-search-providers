<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Data;

final class SearchResult
{
    /**
     * @param  array<string, mixed>  $providerMetadata
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $pageUrl,
        public readonly ?string $imageUrl,
        public readonly ?string $thumbnailUrl = null,
        public readonly ?string $sourceDomain = null,
        public readonly ?string $snippet = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?float $score = null,
        public readonly array $providerMetadata = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            title: (string) ($attributes['title'] ?? ''),
            pageUrl: self::normalizeUrl($attributes['page_url'] ?? $attributes['pageUrl'] ?? null),
            imageUrl: self::normalizeUrl($attributes['image_url'] ?? $attributes['imageUrl'] ?? null),
            thumbnailUrl: self::normalizeUrl($attributes['thumbnail_url'] ?? $attributes['thumbnailUrl'] ?? null),
            sourceDomain: self::normalizeString($attributes['source_domain'] ?? $attributes['sourceDomain'] ?? null),
            snippet: self::normalizeString($attributes['snippet'] ?? null),
            width: self::normalizeInt($attributes['width'] ?? null),
            height: self::normalizeInt($attributes['height'] ?? null),
            score: self::normalizeFloat($attributes['score'] ?? null),
            providerMetadata: is_array($attributes['provider_metadata'] ?? null)
                ? $attributes['provider_metadata']
                : (is_array($attributes['providerMetadata'] ?? null) ? $attributes['providerMetadata'] : []),
        );
    }

    public function fingerprint(): string
    {
        $page = strtolower(trim((string) $this->pageUrl));
        $image = strtolower(trim((string) $this->imageUrl));

        return sha1($page.'|'.$image);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'fingerprint' => $this->fingerprint(),
            'title' => $this->title,
            'page_url' => $this->pageUrl,
            'image_url' => $this->imageUrl,
            'thumbnail_url' => $this->thumbnailUrl,
            'source_domain' => $this->sourceDomain,
            'snippet' => $this->snippet,
            'width' => $this->width,
            'height' => $this->height,
            'score' => $this->score,
            'provider_metadata' => $this->providerMetadata,
        ];
    }

    private static function normalizeString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private static function normalizeUrl(mixed $value): ?string
    {
        return self::normalizeString($value);
    }

    private static function normalizeInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private static function normalizeFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
