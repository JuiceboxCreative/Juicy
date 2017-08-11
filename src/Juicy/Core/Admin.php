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
        //remove comments link from admin menu, remove this filter if your site uses WP comments
        add_action('admin_menu',                [$this, 'remove_menu_items'], 99);
        add_action('admin_menu',                [$this, 'update_menu'], 99 );
        add_filter('parent_file',               [$this, 'parent_file'], 99);

        //Login page customisations
        add_filter('login_headerurl',           [$this, 'login_url'], 999);
        add_filter('login_headertitle',         [$this, 'login_title'], 999);

        // adding it to the admin area
        add_filter('admin_footer_text',         [$this, 'custom_admin_footer']);

        //Add custom functions to twig
        add_filter('get_twig',                  [$this, 'add_to_twig']);

        // customise WYSIWYG
        add_filter('mce_buttons_2',             [$this, 'customise_wysiwyg']);
        add_filter('tiny_mce_before_init',      [$this, 'add_styles_to_wysiwyg']);
        add_action('after_setup_theme',         [$this, 'add_wysiwyg_stylesheet']);

        // Remove options for clients to deactivate plugins
        add_filter('plugin_action_links',       [$this, 'jb_remove_deactivate'], 10, 4 );

        // Auto activate plugins
        add_action('admin_init',                [$this, 'jb_activate_plugins'] );

        // Before Gravity forms sends an email, add our BCC
        add_action('gform_pre_send_email',      [$this, 'add_bcc'], 99, 3 );

        if (is_user_logged_in()) {
            add_action('wp_head',               [$this, 'admin_css'], 1);
            add_action('admin_head',            [$this, 'admin_css'], 1);
        }

        add_action('login_enqueue_scripts',     [$this, 'login_css']);

        add_action('admin_enqueue_scripts',     [$this, 'add_admin_scripts']);

        add_action('admin_bar_menu', [$this, 'add_env_to_admin_bar']);

        add_filter( 'tiny_mce_before_init', function ( $mce ) {
            $mce['body_class'] .= ' article-content';
            return $mce;
        });

        if (function_exists('acf_add_options_page')) {
            $this->options_pages();
        }
    }

    public function add_env_to_admin_bar( \WP_Admin_Bar $admin_bar )
    {
        $env = env('WP_ENV');
        $dashicon = $env == 'production' ? 'site' : 'generic';

        $admin_bar->add_menu([
                'id' => 'wp-admin-env',
                'title' => '<span class="wpadmin-env__dashicon dashicons dashicons-admin-' . $dashicon . '"></span>' . ucwords($env) . '</span>',
                'meta'   => [ 'class' => 'wpadmin-env wpadmin-env--' . $env ]
            ]
        );
    }

    public function add_admin_scripts()
    {
        wp_enqueue_script( 'acf_fc_jpg', get_stylesheet_directory_uri() . '/js/admin.js', ['jquery', 'acf-input'] );

        wp_localize_script( 'acf_fc_jpg', 'acfJpgData', [
            'themeUri'  => get_stylesheet_directory_uri()
        ]);
    }

    public function login_css()
    {
        wp_enqueue_style( 'admin_font', '//fonts.googleapis.com/css?family=Open+Sans:300,400,700' );
        wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/css/login.min.css', ['admin_font'] );
    }

    public function admin_css()
    {
        wp_enqueue_style( 'admin_font', '//fonts.googleapis.com/css?family=Open+Sans:300,400,700' );
        wp_enqueue_style( 'custom-admin', get_stylesheet_directory_uri() . '/css/admin.min.css', ['admin_font'] );
    }

    /**
     * Set the correct active class when we are on the menu page. Required as we altered the menu structure.
     */
    public function parent_file( $file )
    {
        // Current page
        $self = preg_replace('|^.*/wp-admin/network/|i', '', $_SERVER['PHP_SELF']);
        $self = preg_replace('|^.*/wp-admin/|i', '', $self);
        $self = preg_replace('|^.*/plugins/|i', '', $self);
        $self = preg_replace('|^.*/mu-plugins/|i', '', $self);

        if ( $file == 'themes.php' && $self == 'nav-menus.php' ) {
            $file = $self;
        }

        return $file;
    }

    public function update_menu()
    {
        global $submenu;

        // remove menus from under appearance if it's available to user
        if (isset($submenu['themes.php'])) {
            foreach ( $submenu['themes.php'] as $key => $value ) {
                if ( $value[2] == 'nav-menus.php' ) {
                    unset( $submenu['themes.php'][$key] );
                }
            }

            // add it to top level
            add_menu_page(
                'Menus',
                'Menus',
                'edit_theme_options',
                'nav-menus.php',
                '',
                "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMCAyMC40IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAzMCAyMC40Ij48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZmlsbD0iIzk5OSIgZD0iTTEuOCAzLjZoMjYuNGMxIDAgMS44LS44IDEuOC0xLjhTMjkuMiAwIDI4LjIgMEgxLjhDLjggMCAwIC44IDAgMS44cy44IDEuOCAxLjggMS44em0yNi40IDQuOEgxLjhjLTEgMC0xLjguOC0xLjggMS44Uy44IDEyIDEuOCAxMmgyNi40YzEgMCAxLjgtLjggMS44LTEuOHMtLjgtMS44LTEuOC0xLjh6bTAgOC40SDEuOGMtMSAwLTEuOC44LTEuOCAxLjhzLjggMS44IDEuOCAxLjhoMjYuNGMxIDAgMS44LS44IDEuOC0xLjhzLS44LTEuOC0xLjgtMS44eiIvPjwvc3ZnPg==",
                59
            );
        }
    }

    /**
     * Remove menu items if not juicebox
     */
    public function remove_menu_items ()
    {
        $user = wp_get_current_user();

        if ( strtolower($user->data->user_login) !== 'juicebox' ) {
            remove_menu_page( 'amazon-web-services' );
            remove_menu_page( 'tools.php' );
            remove_menu_page( 'edit-comments.php' );
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
        return $twig;
    }

    // Custom Backend Footer
    public function custom_admin_footer()
    {
        _e('<span id="footer-thankyou">Developed by <a href="http://juicebox.com.au" target="_blank">Juicebox</a></span>.', 'wordpress');
    }

    // changing the logo link from wordpress.org to your site
    public function login_url()
    {
        return get_bloginfo('url');
    }

    // changing the alt text on the logo to show your site name
    public function login_title()
    {
        return get_bloginfo('name');
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
