<?php

namespace App\Map\Tile\SpecialTile;

use App\Map\Actions\Action;
use App\Map\Actions\DoNothing;
use App\Map\Actions\UpdateResourceCount;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesTick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasMenu;
use App\Map\Tile\Purchasable;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\SavesMenu;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Tile;
use App\Map\Tile\Traits\BaseTileTrait;

final class TradingPostTile implements Tile, HasMenu, SavesMenu, HandlesTick, HasBorder, Purchasable
{
    use BaseTileTrait;

    public ?Resource $input = null;

    public ?Resource $output = null;

    public function getBorderStyle(): BorderStyle
    {
        if ($this->input) {
            return new BorderStyle($this->input->getBaseColor(), 4);
        }

        return new BorderStyle('black', 2);
    }

    public function getMenu(): Menu
    {
        return new Menu(
            hasMenu: $this,
            viewPath: 'menu.tradingPost',
            viewData: [
                'form' => [
                    'input' => $this->input->value ?? null,
                    'output' => $this->output->value ?? null,
                ],
                'tile' => $this,
            ],
        );
    }

    public function saveMenu(array $data): void
    {
        $this->input = Resource::tryFrom($data['input']);
        $this->output = Resource::tryFrom($data['output']);
    }

    public function handleTick(MapGame $game): Action
    {
        if (! $this->input || ! $this->output) {
            return new DoNothing();
        }

        if ($game->{$this->input->getCountPropertyName()} < 4) {
            return new DoNothing();
        }

        return new UpdateResourceCount(...[
            $this->input->getCountPropertyName() => -4,
            $this->output->getCountPropertyName() => 1,
        ]);
    }

    public function getName(): string
    {
        return 'TradingPostTile';
    }

    public function getPrice(MapGame $game): Price
    {
        return new Price(wood: 1);
    }
}
