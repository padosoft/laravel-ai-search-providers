<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Data;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, SearchResult>
 */
final class SearchResultCollection implements Countable, IteratorAggregate
{
    /**
     * @var array<int, SearchResult>
     */
    private array $items = [];

    /**
     * @param  iterable<int, SearchResult|array<string, mixed>>  $items
     */
    public function __construct(iterable $items = [])
    {
        foreach ($items as $item) {
            $this->items[] = $item instanceof SearchResult ? $item : SearchResult::fromArray($item);
        }
    }

    public function add(SearchResult $result): self
    {
        $clone = clone $this;
        $clone->items[] = $result;

        return $clone;
    }

    /**
     * @return array<int, SearchResult>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            static fn (SearchResult $result): array => $result->toArray(),
            $this->items,
        );
    }

    public function first(): ?SearchResult
    {
        return $this->items[0] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
