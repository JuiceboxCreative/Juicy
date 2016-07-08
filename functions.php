<?php
//Include composer autoloader
include dirname(ABSPATH) . "/vendor/autoload.php";

$timber = new \Timber\Timber();

use Juicy\Config\Shortcodes;
use Juicy\Config\Plugins;
use Juicy\Config\ThemeSupport;
use Juicy\Config\CustomPostTypes;
use Juicy\Config\CustomTaxonomies;
use Juicy\Config\Menus;
use Juicy\Config\Assets;

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
// Required plugins
Plugins::register();

// Register support of certain theme features
ThemeSupport::register();

// Register CPT
CustomPostTypes::register();

// Register Custom Taxonomies
CustomTaxonomies::register();

// Register WordPress menus
Menus::register();

// Load JS/CSS
Assets::load();
