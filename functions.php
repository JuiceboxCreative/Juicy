<?php
//Include composer autoloader
include dirname(ABSPATH) . "/vendor/autoload.php";

//Make sure timber plugin is activated
if (!class_exists('Timber')) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . admin_url('plugins.php#timber') . '">' . admin_url('plugins.php') . '</a></p></div>';
    });
    return;
}else {
    //Setup Timber variables
    //Get theme views to override any plugin views
    Timber::$locations = __DIR__ . "/modules";
}

use Juicy\Config\Shortcodes;
use Juicy\Config\CustomFields;
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
