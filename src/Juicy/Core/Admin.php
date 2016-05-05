<?php

namespace Juicy\Core;

use Timber;
use Twig_SimpleFunction;

class Admin
{

    public function __construct()
    {
        //Login page customisations
        add_action('login_enqueue_scripts', array($this, 'login_css'), 10);
        add_filter('login_headerurl', array($this, 'login_url'));
        add_filter('login_headertitle', array($this, 'login_title'));

        // adding it to the admin area
        add_filter('admin_footer_text', array($this, 'custom_admin_footer'));

        if (function_exists('acf_add_options_page')) {
            $this->options_pages();
        }

        //Add custom functions to twig
        add_filter('get_twig', array($this, 'add_to_twig'));

        // customise WYSIWYG
        add_filter('mce_buttons_2', array($this, 'customise_wysiwyg'));
        add_filter('tiny_mce_before_init', array($this, 'add_styles_to_wysiwyg'));
        add_action('after_setup_theme', array($this, 'add_wysiwyg_stylesheet'));

        // Create default pages
        add_action( 'after_setup_theme', array( $this, 'add_default_pages' ) );
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
        _e('<span id="footer-thankyou">Developed by <a href="http://Juicy.com.au" target="_blank">Juicy</a></span>.', 'wordpress');
    }

    //Login page CSS
    public function login_css()
    {
        wp_enqueue_style('jb_login_css', get_template_directory_uri() . '/css/login.css', false );
    }

    // changing the logo link from wordpress.org to your site
    public function login_url()
    {
        return "http://www.Juicy.com.au";
    }

    // changing the alt text on the logo to show your site name
    public function login_title()
    {
        return "Site by Juicy";
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
                'block' => 'span',
                'classes' => 'lead',
                'wrapper' => true,
            )
        );
        // Insert the array, JSON ENCODED, into 'style_formats'
        $init_array['style_formats'] = json_encode( $style_formats );

        return $init_array;
    }

    public function add_wysiwyg_stylesheet()
    {
        add_editor_style('css/editor-style.css');
    }

    public function add_default_pages()
    {
        $pattern = dirname(dirname(__DIR__)).'/pages/*.txt';

        $search = array(
            '{{clientName}}',
            '{{clientEmail}}',
            '{{clientAddress}}'
        );

        $replace = array(
            'Juicy Creative',
            'info@juciebox.com.au',
            '91 Brisbane St, Perth WA'
        );

        foreach (glob($pattern) as $page) {
            $title = basename($page, '.txt');

            // If the page doesn't exist, create it
            if (! get_page_by_title($title)) {
                $content = file_get_contents($page);
                $content = str_replace($search, $replace, $content);

                $insert = array(
                    'post_title'    => $title,
                    'post_content'  => $content,
                    'post_status'   => 'publish',
                    'post_type'     => 'page'
                );

                wp_insert_post($insert);
            }
        }
    }
}
