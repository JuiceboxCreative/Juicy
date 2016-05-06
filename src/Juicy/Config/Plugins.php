<?php

namespace Juicy\Config;

class Plugins {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    public static $plugins = array(

        // Pre-packaged plugins
        array(
            'name'               => 'Advanced Custom Fields Pro',
            'slug'               => 'advanced-custom-fields-pro',
            'source'             => 'advanced-custom-fields-pro.zip',
            'required'           => true,
            'force_activation'   => true,
            'force_deactivation' => true,
        ),
        array(
            'name'              => 'iThemes Security Pro',
            'slug'              => 'ithemes-security-pro',
            'source'            => 'ithemes-security-pro.zip',
            'required'          => true,
            'force_activation'  => true,
            'force_deactivation'  => true,
        ),
        array(
            'name'              => 'WP Offload S3',
            'slug'              => 'wp-offload-s3',
            'source'            => 'wp-offload-s3.zip',
            'required'          => true,
            'force_activation'  => true,
            'force_deactivation'  => true,
        ),
        array(
            'name'              => 'WP Offload S3 Enable Media Replace',
            'slug'              => 'wp-offload-s3-enable-media-replace',
            'source'            => 'wp-offload-s3-enable-media-replace.zip',
            'required'          => true,
            'force_activation'  => true,
            'force_deactivation'  => true,
        ),

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
        array(
            'name'      => 'Enable Media Replace',
            'slug'      => 'enable-media-replace',
            'required'  => true,
            'force_activation'   => true,
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
        require_once get_stylesheet_directory() . '/src/PluginActivation/PluginActivation.php';

        static::$config['default_path'] = get_stylesheet_directory() . '/src/PluginActivation/plugins/';

        add_action( 'tgmpa_register', array( __CLASS__, 'register_required_plugins' ));
    }

    public static function register_required_plugins()
    {
        tgmpa( static::$plugins, static::$config );
    }
}
