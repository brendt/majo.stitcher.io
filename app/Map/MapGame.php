<?php

namespace App\Map;

use App\Map\Inventory\Inventory;
use App\Map\Inventory\Item;
use App\Map\Inventory\Item\Seed;
use App\Map\Inventory\ItemForTile;
use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\FishLayer;
use App\Map\Layer\FlaxLayer;
use App\Map\Layer\GoldLayer;
use App\Map\Layer\IslandLayer;
use App\Map\Layer\RiverLayer;
use App\Map\Layer\StoneLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\BasicNoise;
use App\Map\Noise\ScatterNoise;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTick;
use App\Map\Tile\HasMenu;
use App\Map\Tile\CalculatesResourcePerTick;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\SavesMenu;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;
use Closure;
use Generator;
use Illuminate\Support\Facades\Session;

final class MapGame
{
    public function __construct(
        public int $seed,
        public BaseLayer $baseLayer,
        public int $woodCount = 0,
        public int $stoneCount = 0,
        public int $goldCount = 0,
        public int $fishCount = 0,
        public int $flaxCount = 0,
        public int $gameTime = 0,
        public bool $paused = false,
        public ?Menu $menu = null,
        private array $tiles = [],
        public Inventory $inventory = new Inventory(),
        public ?Item $selectedItem = null,
    ) {}

    public static function resolve(): self
    {
        if ($fromSession = Session::get('map')) {
            $game = unserialize($fromSession);
        } else {
            $game = self::init(time());
        }

        if (request()->query->has('cheat')) {
            foreach (Resource::cases() as $case) {
                $property = $case->getCountPropertyName();

                while ($game->{$property} < 1000) {
                    $game->{$property} += 1000;
                }
            }
        }

        return $game;
    }

    public function persist(): self
    {
        $this->updateGameTime();

        Session::put('map', serialize($this));

        return $this;
    }

    public function destroy(): void
    {
        Session::remove('map');
    }

    public static function init(int $seed): self
    {
        $basicNoise = new BasicNoise($seed);
        $scatterNoise = new ScatterNoise($seed);

        $baseLayer = (new BaseLayer(width: 100, height: 100))
            ->add(new BiomeLayer($basicNoise))
            ->add(new WoodLayer($scatterNoise))
            ->add(new FlaxLayer($scatterNoise))
            ->add(new StoneLayer($scatterNoise))
            ->add(new FishLayer($scatterNoise))
            ->add(new GoldLayer($scatterNoise))
            ->generate();

        $game = new self(
            seed: $seed,
            baseLayer: $baseLayer,
            gameTime: time(),
        );

        foreach ($baseLayer->loop() as $tile) {
            if (
                $tile::class === LandTile::class
                || $tile::class === WaterTile::class
            ) {
                continue;
            }

            $game->addTile($tile);
        }

        $game->inventory->add(new Seed());

        return $game;
    }

    public function showMenu(Point $point): self
    {
        $tile = $this->getTile($point);

        if ($tile instanceof HasMenu) {
            $this->menu = $tile->getMenu();
            $this->paused = true;
        }

        return $this;
    }

    public function closeMenu(): self
    {
        $this->menu = null;
        $this->paused = false;

        return $this;
    }

    public function saveMenu(array $data): self
    {
        if (! $this->menu) {
            return $this;
        }

        $hasMenu = $this->menu->hasMenu;

        if ($hasMenu instanceof SavesMenu) {
            $hasMenu->saveMenu($data);
        }

        $this->closeMenu();

        return $this;
    }

    public function handleClick(Point $point): self
    {
        $tile = $this->getTile($point);

        if ($this->selectedItem instanceof ItemForTile) {
            $this->useItem($this->selectedItem, $tile);

            return $this;
        }

        if ($tile instanceof HandlesClick) {
            $action = $tile->handleClick($this);

            $action($this);
        }

        return $this;
    }

    private function updateGameTime(): void
    {
        $oldTime = $this->gameTime;

        $newTime = time();

        $difference = $newTime - $oldTime;

        if ($difference <= 0) {
            return;
        }

        // Prevent time traveling
        if ($difference > 100) {
            $this->gameTime = $newTime;
            return;
        }

        foreach ($this->loop() as $tile) {
            if (! $tile instanceof HandlesTick) {
                continue;
            }

            foreach (range(1, $difference) as $tick) {
                $action = $tile->handleTick($this);
                $action($this);
            }
        }

        $this->gameTime = $newTime;
    }

    public function resourcePerTick(Resource $resource): int
    {
        $count = 0;

        foreach ($this->loopOwnTiles() as $tile) {
            if (! $tile instanceof CalculatesResourcePerTick) {
                continue;
            }

            $count += $tile->getResourcePerTick($this, $resource);
        }

        return $count;
    }

    /**
     * @return Tile[]
     */
    public function getNeighbours(Tile $tile, int $radius = 1): array
    {
        $startX = $tile->getPoint()->x;
        $startY = $tile->getPoint()->y;

        $neighbours = [];

        for ($x = $startX - $radius; $x <= $startX + $radius; $x++) {
            for ($y = $startY - $radius; $y <= $startY + $radius; $y++) {
                $neighbours[] = $this->getTile(new Point($x, $y));
            }
        }

        return array_filter($neighbours);
    }

    public function findClosestTo(Tile $tile, Closure $filter, int $radius = 1): ?Tile
    {
        $startX = $tile->getPoint()->x;
        $startY = $tile->getPoint()->y;

        for ($r = 1; $r <= $radius; $r++) {
            for ($x = $startX - $r; $x <= $startX + $r; $x++) {
                for ($y = $startY - $r; $y <= $startY + $r; $y++) {
                    $tile = $this->getTile(new Point($x, $y));

                    if (! $tile) {
                        continue;
                    }

                    if ($filter($tile)) {
                        return $tile;
                    }
                }
            }
        }

        return null;
    }

    public function getTile(Point $point): ?Tile
    {
        return $this->tiles[$point->x][$point->y] ?? $this->baseLayer->get($point);
    }

    public function upgradeTile(Point $point, string $upgradeTo): self
    {
        $tile = $this->getTile($point);

        if (! $tile instanceof Upgradable) {
            return $this;
        }

        $upgradeTile = null;

        foreach ($tile->canUpgradeTo($this) as $canUpgradeTo) {
            if ($canUpgradeTo->getName() !== $upgradeTo) {
                continue;
            }

            $upgradeTile = $canUpgradeTo;

            break;
        }

        if (! $upgradeTile) {
            return $this;
        }

        $price = $upgradeTile->getPrice($this);

        if (! $this->canPay($price)) {
            return $this;
        }

        $this->pay($price);
        $this->setTile($tile->getPoint(), $upgradeTile);
        $this->closeMenu();

        return $this;
    }

    public function canPay(Price $price): bool
    {
        return $this->woodCount >= $price->wood
            && $this->goldCount >= $price->gold
            && $this->stoneCount >= $price->stone
            && $this->flaxCount >= $price->flax
            && $this->fishCount >= $price->fish;
    }

    public function pay(Price $price): void
    {
        $this->woodCount -= $price->wood;
        $this->goldCount -= $price->gold;
        $this->stoneCount -= $price->stone;
        $this->flaxCount -= $price->flax;
        $this->fishCount -= $price->fish;
    }

    public function setTile(Point $point, Tile $newTile): void
    {
        $this->tiles[$point->x][$point->y] = $newTile;

        $this->baseLayer->remove($point);
    }

    public function loop(): Generator
    {
        foreach ($this->baseLayer->loop() as $tile) {
            yield $tile;
        }

        foreach ($this->tiles as $row) {
            foreach ($row as $tile) {
                yield $tile;
            }
        }
    }

    public function getOwnTiles(): array
    {
        return $this->tiles;
    }

    public function getAllTiles(): array
    {
        return iterator_to_array($this->loop());
    }

    private function loopOwnTiles(): Generator
    {
        foreach ($this->tiles as $row) {
            foreach ($row as $tile) {
                yield $tile;
            }
        }
    }

    private function addTile(Tile $tile): void
    {
        $this->setTile($tile->getPoint(), $tile);
    }

    public function selectItem(string $itemId): self
    {
        $item = $this->inventory->findItem($itemId);

        if (! $item) {
            return $this;
        }

        if ($this->selectedItem?->getId() === $item->getId()) {
            $this->selectedItem = null;
        } else {
            $this->selectedItem = $item;
        }

        return $this;
    }

    public function useItem(ItemForTile $item, Tile $tile): self
    {
        $itemId = $item->getId();

        $item->useOn($tile, $this);
        $this->inventory->remove($item);
        $this->selectedItem = $this->inventory->findItem($itemId);

        return $this;
    }

    public function toArray(): array
    {
        $class = [];

        if ($this->selectedItem) {
            $class[] = 'clickable-LandTile';
        }

        return [
            'class' => implode(' ', $class),
        ];
    }
}
