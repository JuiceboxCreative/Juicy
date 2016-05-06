<?php

namespace Juicy\Config;

class Plugins {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    public static $plugins = array(

        // Juicebox hosted plugins
        array(
            'name'               => 'IconBox',
            'slug'               => 'iconbox',
            'source'             => "http://wp-updater.dev.juicebox.com.au/?action=download&slug=iconbox",
            'required'           => true,
            'force_activation'   => true,
            'force_deactivation' => true,
        ),

        // WordPress Plugin Repository.
        array(
            'name'      => 'Nested Pages',
            'slug'      => 'wp-nested-pages',
            'required'  => true,
            'force_activation'   => true,
        ),
        array(
            'name'      => 'WP Migrate DB',
            'slug'      => 'wp-migrate-db',
            'required'  => true,
            'force_activation'   => true,
        ),
        array(
            'name'      => 'Timber Library',
            'slug'      => 'timber-library',
            'required'  => true,
            'force_activation'   => true,
        ),
        array(
            'name'      => 'Wordpress SEO',
            'slug'      => 'wordpress-seo',
            'required'  => true,
            'force_activation'   => true,
        ),
        array(
            'name'      => 'Google Analytics Dashboard for WP',
            'slug'      => 'google-analytics-dashboard-for-wp',
            'required'  => true,
            'force_activation'   => true,
        ),
        array(
            'name'      => 'W3 Total Cache',
            'slug'      => 'w3-total-cache',
            'required'  => false,
            'force_activation'   => false,
        ),
        array(
            'name'      => 'Redirection',
            'slug'      => 'redirection',
            'required'  => false,
            'force_activation'   => false,
        ),
    );

    public static $config = array(
        'parent_slug'       => 'plugins.php',
        'strings'           => array(
            'menu_title'    => 'TGMPA'
        )
    );

    public static function register()
    {
        // Add required plugins using class
        require_once get_template_directory() . '/src/PluginActivation/PluginActivation.php';

        static::$config['default_path'] = get_template_directory() . '/src/PluginActivation/plugins/';

        add_action( 'tgmpa_register', array( __CLASS__, 'register_required_plugins' ));
    }

    public static function register_required_plugins()
    {
        tgmpa( static::$plugins, static::$config );
    }
}
