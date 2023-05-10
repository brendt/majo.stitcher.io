<?php

namespace App\Map;

use App\Map\Actions\UpdateResourceCount;
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
use App\Map\Layer\LandLayer;
use App\Map\Layer\StoneLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\PerlinGenerator;
use App\Map\Noise\SimplexGenerator;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasMenu;
use App\Map\Tile\HasResource;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\SavesMenu;
use App\Map\Tile\SpecialTile\TradingPostTile;
use App\Map\Tile\SpecialTile\TradingPostXLTile;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;
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
        $perlin = new PerlinGenerator($seed);

        $baseLayer = (new BaseLayer(width: 100, height: 70))
            ->add(new TemperatureLayer($perlin))
            ->add(new ElevationLayer($perlin))
            ->add(new IslandLayer())
            ->add(new BiomeLayer())
            ->add(new LandLayer($perlin))
            ->add(new WoodLayer($perlin))
            ->add(new StoneLayer($perlin))
            ->add(new FishLayer($perlin))
            ->add(new GoldLayer($perlin))
            ->add(new FlaxLayer($perlin))
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

    public function showMenu(int $x, int $y): self
    {
        $tile = $this->getTile($x, $y);

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

    public function handleClick(int $x, int $y): self
    {
        $tile = $this->getTile($x, $y);

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
            if (! $tile instanceof HandlesTicks) {
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
            if (! $tile instanceof HandlesTicks) {
                continue;
            }

            $tickAction = null;

            $tickAction = match (true) {
                $tile instanceof TradingPostTile, $tile instanceof TradingPostXLTile => $tile->handleTick($this),
                $tile instanceof HasResource && $tile->getResource() === $resource => $tile->handleTick($this),
                default => null,
            };

            if (! $tickAction instanceof UpdateResourceCount) {
                continue;
            }

            $count += $tickAction->{$resource->getCountPropertyName()};
        }

        return $count;
    }

    /**
     * @return Tile[]
     */
    public function getNeighbours(Tile $tile, int $radius = 1): array
    {
        $startX = $tile->getX();
        $startY = $tile->getY();

        $neighbours = [];

        for ($x = $startX - $radius; $x <= $startX + $radius; $x++) {
            for ($y = $startY - $radius; $y <= $startY + $radius; $y++) {
                $neighbours[] = $this->getTile($x, $y);
            }
        }

        return array_filter($neighbours);
    }

    public function getTile(int $x, int $y): ?Tile
    {
        return $this->tiles[$x][$y] ?? $this->baseLayer->get($x, $y);
    }

    public function upgradeTile(int $x, int $y): self
    {
        $tile = $this->getTile($x, $y);

        if (! $tile instanceof Upgradable) {
            return $this;
        }

        $price = $tile->getUpgradePrice();

        if (! $this->canPay($price)) {
            return $this;
        }

        $upgradeTile = $tile->getUpgradeTile();

        $this->pay($price);
        $this->setTile($tile->x, $tile->y, $upgradeTile);
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

    public function setTile(int $x, int $y, Tile $newTile): void
    {
        $this->tiles[$x][$y] = $newTile;
        $this->baseLayer->remove($x, $y);
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
        $this->setTile($tile->getX(), $tile->getY(), $tile);
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
        $class= [];

        if ($this->selectedItem) {
            $class[] = 'clickable-LandTile';
        }

        return [
            'class' => implode(' ', $class),
        ];
    }
}