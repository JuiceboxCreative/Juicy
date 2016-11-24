<?php

namespace Juicy\Core;

use Timber;
use Twig_SimpleFunction;

class Admin
{
    protected $defaultPlugins = array();

    /**
     * Email(s) for Gravity form submissions to BCC
     * @var string
     */
    private $bccEmail = 'marketing-emails@juicebox.com.au';

    public function __construct()
    {
        //Login page customisations
        add_filter('login_headerurl',           array($this, 'login_url'));
        add_filter('login_headertitle',         array($this, 'login_title'));

        // adding it to the admin area
        add_filter('admin_footer_text',         array($this, 'custom_admin_footer'));

        //Add custom functions to twig
        add_filter('get_twig',                  array($this, 'add_to_twig'));

        // customise WYSIWYG
        add_filter('mce_buttons_2',             array($this, 'customise_wysiwyg'));
        add_filter('tiny_mce_before_init',      array($this, 'add_styles_to_wysiwyg'));
        add_action('after_setup_theme',         array($this, 'add_wysiwyg_stylesheet'));

        // Remove options for clients to deactivate plugins
        add_filter( 'plugin_action_links',      array($this, 'jb_remove_deactivate'), 10, 4 );

        // Auto activate plugins
        add_action( 'admin_init',               array($this, 'jb_activate_plugins') );

        // Before Gravity forms sends an email, add our BCC
        add_action( 'gform_pre_send_email',     array($this, 'add_bcc'), 99, 3 );

        if (function_exists('acf_add_options_page')) {
            $this->options_pages();
        }
    }

    // Automatically activate required plugins.
    public function jb_activate_plugins()
    {
        $current_plugins = get_option('active_plugins'); // get active plugins

        foreach ( $this->defaultPlugins as $plugin ) {
            // If the plugin isnt currently active
            if ( ! in_array( $plugin, $current_plugins ) ) {
                activate_plugin($plugin);
            }
        }
    }

    // Removes the ability for core plugins to be deactivated.
    public function jb_remove_deactivate( $actions, $plugin_file, $plugin_data, $context )
    {
        if ( in_array( $plugin_file, $this->defaultPlugins ) && isset($actions['deactivate']) ) {
            unset($actions['deactivate']);
        }

        return $actions;
    }

    public function options_pages()
    {
        acf_add_options_page(array(
            'page_title'    => 'Theme General Settings',
            'menu_title'    => 'Theme Settings',
            'menu_slug'     => 'theme-general-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));
    }

    public function add_to_twig($twig)
    {
        $twig->addFunction(new Twig_SimpleFunction('theme_option', array($this, 'get_theme_option')));

        return $twig;
    }

    public function get_theme_option($option)
    {
        return get_field($option, 'option');
    }

    // Custom Backend Footer
    public function custom_admin_footer()
    {
        _e('<span id="footer-thankyou">Developed by <a href="http://juicebox.com.au" target="_blank">Juicebox</a></span>.', 'wordpress');
    }

    // changing the logo link from wordpress.org to your site
    public function login_url()
    {
        return "http://www.juicebox.com.au";
    }

    // changing the alt text on the logo to show your site name
    public function login_title()
    {
        return "Site by Juicebox";
    }

    public function customise_wysiwyg($buttons)
    {
        //Add style selector to the beginning of the toolbar
        array_unshift($buttons, 'styleselect');
        $buttons[] = 'hr';

        return $buttons;
    }

    public function add_styles_to_wysiwyg($init_array)
    {
        $init_array['block_formats'] = "Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;";
        $init_array['menubar'] = true;

        $style_formats = array(
            // Each array child is a format with it's own settings
            array(
                'title' => 'Large Paragraph',
                'selector' => 'p',
                'classes' => 'lead',
            )
        );

        // Insert the array, JSON ENCODED, into 'style_formats'
        $init_array['style_formats'] = json_encode( $style_formats );

        return $init_array;
    }

    public function add_wysiwyg_stylesheet()
    {
        if (file_exists(get_stylesheet_directory() . '/css/editor-style.min.css')) {
            add_editor_style('css/editor-style.min.css');
        }
        else {
            add_editor_style('css/editor-style.css');
        }
    }

    /**
     * Add BCC to all GF emails
     * @param array $args
     */
    public function add_bcc( $args, $message_format, $notification )
    {
        // Bail out if we are in dev
        if ( Helpers::is_dev() ) {
            return $args;
        }

        // If there is already a BCC and if $this->bccEmail isn't already present then add it
        if ( isset($args['headers']['Bcc']) && ! Helpers::string_exists( $this->bccEmail, $args['headers']['Bcc'] ) ) {
            $args['headers']['Bcc'] .= ", {$this->bccEmail}";
        } else {
            $args['headers']['Bcc'] = "Bcc: {$this->bccEmail}";
        }

        return $args;
    }
}
