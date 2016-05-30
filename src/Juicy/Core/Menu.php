<?php

namespace Juicy\Core;

use Timber\Menu as TimberMenu;

class Menu extends TimberMenu
{
    public $MenuItemClass = '\\Juicy\\Core\\MenuItem';
    public $PostClass = '\\Juicy\\Core\\Post';
}
