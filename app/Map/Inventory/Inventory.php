<?php

namespace App\Map\Inventory;

use Generator;

/**
 * @property \App\Map\Inventory\Item[] $items
 */
final class Inventory
{
    public function __construct(
        private array $items = [],
    ) {}

    /**
     * @return \Generator|\App\Map\Inventory\Item[]
     */
    public function loop(): Generator
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }

    /**
     * @return \Generator|\App\Map\Inventory\Item[]
     */
    public function loopGrouped(): Generator
    {
        $groups = [];

        foreach ($this->items as $item) {
            $groups[$item->getId()] ??= 0;
            $groups[$item->getId()] += 1;
        }

        foreach ($groups as $itemId => $count) {
            yield $itemId => $count;
        }
    }

    public function add(Item $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function findItem(string $itemId): ?Item
    {
        foreach ($this->items as $i => $item) {
            if ($item->getId() === $itemId) {
                return $item;
            }
        }

        return null;
    }

    public function remove(Item $itemToBeRemoved): self
    {
        foreach ($this->items as $i => $item) {
            if ($item::class === $itemToBeRemoved::class) {
                unset($this->items[$i]);

                return $this;
            }
        }

        return $this;
    }
}
