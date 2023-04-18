<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Tile\GenericTile\BaseTile;

final class LandTile extends BaseTile
{
    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        return $this->getBiome()->getGrassColor($this);
        return match (true) {
            $this->temperature <= 0.4 => $this->getColdColor(),
            $this->temperature <= 0.8 => $this->getWarmColor(),
            default => $this->getHotColor(),
        };
    }

    public function getColdColor(): string
    {
        $hex = hex($this->elevation);

        return match (true) {
            $this->elevation <= 0.8 => "#00{$hex}66",
            default => "#FFFFFF",
        };
    }

    public function getWarmColor(): string
    {
        $hex = hex($this->elevation);

        return match (true) {
            $this->elevation <= 0.8 => "#00{$hex}00",
            default => "#{$hex}{$hex}{$hex}",
        };
    }

    private function getHotColor(): string
    {
        $hex = hex($this->elevation);

        $redHex = hex($this->elevation / 1.4);

        return match (true) {
            $this->elevation <= 0.8 => "#{$redHex}{$hex}00",
            default => "#{$hex}{$hex}{$hex}",
        };
    }
}
