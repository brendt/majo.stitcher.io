<?php

namespace App\Map\Tile\SpecialTile;

use App\Map\Actions\Action;
use App\Map\Actions\DoNothing;
use App\Map\Actions\UpdateResourceCount;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasMenu;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\SavesMenu;

final class TradingPostXLTile extends BaseTile implements HasMenu, SavesMenu, HandlesTicks, HasBorder
{
    public ?Resource $input = null;

    public ?Resource $output = null;

    public function getColor(): string
    {
        if ($this->output) {
            return $this->output->getBaseColor();
        }

        return 'white';
    }

    public function getBorderStyle(): BorderStyle
    {
        if ($this->input) {
            return new BorderStyle($this->input->getBaseColor(), 6);
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

        if ($game->{$this->input->getCountPropertyName()} < 2) {
            return new DoNothing();
        }

        return new UpdateResourceCount(...[
            $this->input->getCountPropertyName() => -2,
            $this->output->getCountPropertyName() => 2,
        ]);
    }
}
