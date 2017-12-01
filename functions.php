<?php
//Include composer autoloader
include dirname(dirname(ABSPATH)) . "/vendor/autoload.php";

use Juicy\Config\Shortcodes;
use Juicy\Config\ThemeSupport;
use Juicy\Config\Menus;
use Juicy\Config\Assets;
use Juicy\Core\Twig;

// Override Timber registered filters.
new Twig();

/**
 * ------------------
 * Core
 * ------------------
 */
Shortcodes::register();

/**
 * ------------------
 * Config
 * ------------------
 */

// Register support of certain theme features
ThemeSupport::register();

// Register WordPress menus
Menus::register();

// Load JS/CSS
Assets::load();
