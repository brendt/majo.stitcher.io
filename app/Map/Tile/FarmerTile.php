<?php

namespace App\Map\Tile;

interface FarmerTile extends Tile, HasResource, HasBorder, HandlesTick, Purchasable, CalculatesResourcePerTick
{

}
