<?php

namespace App\Map;

use App\Map\Actions\UpdateResourceCount;
use App\Map\Item\HandHeldItem;
use App\Map\Layer\FishLayer;
use App\Map\Layer\FlaxLayer;
use App\Map\Layer\GoldLayer;
use App\Map\Layer\StoneLayer;
use App\Map\Tile\HasMenu;
use App\Map\Item\Item;
use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasResource;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\SavesMenu;
use App\Map\Tile\SpecialTile\TradingPostTile;
use App\Map\Tile\SpecialTile\TradingPostXLTile;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;
use Generator;
use Illuminate\Support\Facades\Session;

/**
 * @property HandHeldItem[] $items
 */
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
        public ?Item $selectedItem = null,
        public int $gameTime = 0,
        public array $handHeldItems = [],
        public bool $paused = false,
        public ?Menu $menu = null,
        private array $tiles = [],
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
        $generator = new PerlinGenerator($seed);

        $baseLayer = (new BaseLayer(width: 100, height: 70))
            ->add(new TemperatureLayer($generator))
            ->add(new ElevationLayer($generator))
            ->add(new BiomeLayer())
            ->add(new LandLayer($generator))
            ->add(new WoodLayer($generator))
            ->add(new StoneLayer($generator))
            ->add(new FishLayer($generator))
            ->add(new GoldLayer($generator))
            ->add(new FlaxLayer($generator))
            ->generate();

        return new self(
            seed: $seed,
            baseLayer: $baseLayer,
            gameTime: time(),
        );
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

        foreach ($this->loop() as $tile) {
            if ($tile instanceof HandlesTicks) {
                $action = $tile->handleTicks($this, $difference);

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
                $tile instanceof TradingPostTile, $tile instanceof TradingPostXLTile => $tile->handleTicks($this, 1),
                $tile instanceof HasResource && $tile->getResource() === $resource => $tile->handleTicks($this, 1),
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
    public function getNeighbours(Tile $tile): array
    {
        return array_filter([
            $this->getTile($tile->getX() - 1, $tile->getY()),
            $this->getTile($tile->getX() + 1, $tile->getY()),
            $this->getTile($tile->getX(), $tile->getY() - 1),
            $this->getTile($tile->getX(), $tile->getY() + 1),
        ]);
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

    private function setTile(int $x, int $y, Tile $newTile): void
    {
        $this->tiles[$x][$y] = $newTile;
    }

    public function loop(): Generator
    {
        foreach ($this->baseLayer->loop() as $tile) {
            yield $this->tiles[$tile->x][$tile->y] ?? $tile;
        }
    }

    public function getOwnTiles(): array
    {
        return $this->tiles;
    }

    private function loopOwnTiles(): Generator
    {
        foreach ($this->tiles as $row) {
            foreach ($row as $tile) {
                yield $tile;
            }
        }
    }
}
