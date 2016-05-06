<?php

namespace Juicy\Config;

class Assets
{
    /**
     * The version of CSS we are using, update when changes are made to the
     * live site to ensure all users get a consistent experience.
     * @var string
     */
    protected static $stylesheetVersion = '1';

    /**
     * The version of JS we are using, update when changes are made to the
     * live site to ensure all users get a consistent experience.
     * @var string
     */
    protected static $scriptVersion = '1';

    /**
     * En-queue required assets
     *
     * @param  string  $filter   The name of the filter to hook into
     * @param  integer $priority The priority to attach the filter with
     */
    public static function load($filter = 'wp_enqueue_scripts', $priority = 5)
    {
        // Register the filter
        add_filter($filter, function () {

            // CSS
            wp_enqueue_style('juicy_styles', get_template_directory_uri() . "/css/main.css", array(), static::$stylesheetVersion);

            // JS
            wp_enqueue_script('juicy_modernizr', get_template_directory_uri() . "/js/modernizr.js", array(), static::$scriptVersion);
            wp_enqueue_script('juicy_script', get_template_directory_uri() . "/js/main.js", array('jquery'), static::$scriptVersion, true);

            wp_localize_script('juicy_script', 'themeData', array(
                'themeDir' => get_template_directory_uri()
            ));

            wp_enqueue_script('juicy_script');

        }, $priority);
    }
}
