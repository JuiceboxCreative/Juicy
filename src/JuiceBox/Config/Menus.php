<?php

namespace JuiceBox\Config;

class Menus
{
    public static $menus = array(
        'header_menu' => 'The Header Menu',
        'footer_menu' => 'The Footer Menu',
        'legal_menu'  => 'Legal Pages'
    );

    /**
     * En-queue required assets
     *
     * @param  string  $action   The name of the action to hook into
     * @param  integer $priority The priority to attach the action with
     */
    public static function register($action = 'init', $priority = 10)
    {
        // Register the action
        add_action($action, function () {
            register_nav_menus(static::$menus);
        }, $priority);
    }
}
