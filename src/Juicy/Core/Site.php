<?php

namespace Juicy\Core;

use Timber\Site as TimberSite;
use Timber\Timber;
use Twig_SimpleFunction;
use Juicy\Config\Menus;

class Site extends TimberSite
{
    /**
     * Menus to initialize
     * @var array
     */
    private $menus = array(
        'main_nav' => 'The Main Menu',   // main nav in header
        'footer_links' => 'Footer Links', // secondary nav in footer
        'legal_links' => 'Legal Links' // secondary nav in footer
    );

    protected $MenuClass = '\\Juicy\\Core\\Menu';

    public function __construct()
    {
        //Add global variables to twig
        add_filter('timber_context', array($this, 'add_to_context'));

        //Add custom functions to twig
        add_filter('get_twig', array($this, 'add_to_twig'));

        //Remove p tags wrapped around images
        add_filter('the_content', array($this, 'filter_ptags_on_images'));

        // clear default wordpress gallery stuff
        add_filter('use_default_gallery_style', '__return_false');

        //Add IE only shims
        add_action('wp_head', array($this, 'add_ie_html5_shim'));

        add_action( 'after_setup_theme', array($this, 'schema_breadcrumbs') );

        // Set additional Timber twig directories.
        Timber::$locations = array(
            get_template_directory() . '/modules',
            get_stylesheet_directory() . '/modules',
        );

        parent::__construct();
    }

    public function add_to_context($context)
    {
        $context['menus'] = array();
        foreach (Menus::$menus as $key => $value) {
            $context['menus'][$key] = new $this->MenuClass($key);
        }

        $context['options'] = get_fields('option');

        $context['is_home'] = is_home();
        $context['is_front_page'] = is_front_page();
        $context['is_logged_in'] = is_user_logged_in();

        $context['site'] = $this;

        return $context;
    }

    /* this is where you can add your own fuctions to twig */
    public function add_to_twig($twig)
    {
        $twig->addFunction(new Twig_SimpleFunction('theme_option', function ($option) {
            return get_field($option, 'option');
        }));

        return $twig;
    }

    //Remove p tags from around images in content
    public function filter_ptags_on_images($content)
    {
        return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    }

    // add ie media query shim to header
    public function add_ie_html5_shim()
    {
        echo '<!--[if lt IE 9]>';
        echo '<script src="'.get_template_directory_uri() . '/js/respond.min.js"></script>';
        echo '<script src="'.get_template_directory_uri() . '/js/selectivizr.js"></script>';
        echo '<![endif]-->';
    }

    public function schema_breadcrumbs()
    {
        if (function_exists('yoast_breadcrumb')) {
            new SchemaOrgBreadcrumbs();
        }
    }
}
