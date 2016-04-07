<?php

namespace App\ItemTypes;


class Coin extends ItemTypeBase {
    const ZOMBIE_FINDABLE = false;
    const FIND_CHANCE = 50;
    const FIND_DECIMAL = true;
    const FIND_MIN = 0;
    const FIND_MAX = 2;
    const NAME = "coin";
}
