<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\DoNothing;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\Purchasable;
use App\Map\Tile\Tile;
use Exception;

final class GoldFarmerTile extends BaseTile implements HasResource, HasBorder, HandlesTicks, HandlesClick, Purchasable
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public readonly float $noise,
    ) {}

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value > 0.8) {
            $value -= 0.3;
        }

        $hex = hex($value);

        return "#{$hex}{$hex}{$hex}";
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFEC53', 4);
    }

    public function handleTick(MapGame $game): Action
    {
        $goldTile = $this->getGoldTile($game);

        $distance = $this->getPoint()->distanceBetween($goldTile->getPoint());

        if (rand(1, $distance) !== 1) {
            return new DoNothing();
        }

        return (new UpdateResourceCount(goldCount: 1));
    }

    public function getResource(): Resource
    {
        return Resource::Gold;
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(goldCount: 1);
    }

    public function getMenu(): Menu
    {
        return new Menu(
            hasMenu: $this,
            viewPath: 'menu.upgrade',
            viewData: [
                'tile' => $this,
            ],
        );
    }

    public function getName(): string
    {
        return 'GoldFarmerTile';
    }

    public function getPrice(MapGame $game): Price
    {
        $goldTile = $this->getGoldTile($game);

        return new Price(wood: 10 - $this->getPoint()->distanceBetween($goldTile->getPoint()));
    }

    private function getGoldTile(MapGame $game): GoldTile
    {
        $goldTile = $game->findClosestTo(
            tile: $this,
            filter: fn(Tile $tile) => $tile instanceof GoldTile,
            radius: 5,
        );

        if (! $goldTile instanceof GoldTile) {
            throw new Exception('No gold tile found');
        }

        return $goldTile;
    }
}
