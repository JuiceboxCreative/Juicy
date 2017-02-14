<?php

namespace Juicy\Core;

use Timber\Site as TimberSite;
use Timber\Timber;
use Twig_SimpleFunction;
use Juicy\Config\Menus;

class Site extends TimberSite
{
    protected $MenuClass = '\\Juicy\\Core\\Menu';
    protected $PostClass = '\\Juicy\\Core\\Post';
    protected $ImageClass = '\\Juicy\\Core\\Image';
    protected $TermClass = '\\Juicy\\Core\\Term';

    protected $google_api_key = false;

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

        // Create typography page if it doesn't exist.
        add_action('init', array($this, 'add_typography_page'));

        // Prevent iconbox files from going to S3.
        add_filter('as3cf_pre_update_attachment_metadata', array($this, 'pre_update_attachment_metadata'), 10, 3);

        add_action('after_setup_theme', array($this, 'schema_breadcrumbs'));

        add_filter('acf/format_value/type=post_object', array($this, 'field_to_jb_post'), 99, 3);

        add_filter('acf/format_value/type=taxonomy', array($this, 'field_to_jb_term'), 99, 3);

        add_filter('acf/format_value/type=image', array($this, 'field_to_jb_image'), 99, 3);

        // Check if post needs a password here, removes it from page.php/single.php
        add_filter('timber_render_file', array($this, 'maybe_load_password_template'));

        // Set additional Timber twig directories.
        Timber::$locations = array(
            get_template_directory() . '/src/',
            get_stylesheet_directory() . '/src/',
            get_stylesheet_directory() . '/src/JuiceBox/Modules/',
        );

        add_action('acf/init', function () {
            if ($this->google_api_key !== false) {
                acf_update_setting('google_api_key', $this->google_api_key);
            }
        });

        // Add GA on production
        if (!empty(env('WP_ENV')) && env('WP_ENV') == 'production') {
            // If GA_ID isset otherwise spit out error
            if (env('GA_ID', false)) {
                if (!empty(env('GA_ID')) && env('GA_ID') !== 'UA-XXXXXXXX-XX') {
                    add_action('wp_head', array($this, 'tracking_code'), 99);
                } else {
                    add_action('admin_notices', array($this, 'no_GA'));
                }
            }
        }

        parent::__construct();
    }

    public function no_GA()
    {
        echo Timber::compile('partials/notice/error.twig', ['message' => 'Please enable Google Analytics by setting the Tracking ID within the .env file.']);
    }

    public function tracking_code()
    {
        wp_enqueue_script( 'jb_analytics', get_stylesheet_directory_uri() . '/js/jb_analytics.js', array( 'jquery' ), '', true );
        echo Timber::compile('partials/ga.twig', ['code' => env('GA_ID')]);
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

    public function schema_breadcrumbs()
    {
        if (function_exists('yoast_breadcrumb')) {
            new SchemaOrgBreadcrumbs();
        }
    }

    public function pre_update_attachment_metadata($pre_update, $data, $post_id)
    {
        if (false !== strpos($data['file'], 'iconbox')) {
            return true; // Abort the upload
        }

        return false;
    }

    /**
     * If post field is set to return an ID turn it into an Post class.
     */
    public function field_to_jb_post($value, $post_id, $field)
    {
        if ($field['return_format'] == 'id' && $value !== false) {
            return new $this->PostClass($value);
        }

        return $value;
    }

    /**
     * If image field is set to return an ID turn it into an Image class.
     */
    public function field_to_jb_image($value, $post_id, $field)
    {
        if ($field['return_format'] == 'id' && $value !== false) {
            return new $this->ImageClass($value);
        }

        return $value;
    }

    /**
     * If term field is set to return an ID turn it into an Term class.
     */
    public function field_to_jb_term($value, $term_id, $field)
    {
        if ($field['return_format'] == 'id' && $value !== false) {
            return new $this->TermClass($value);
        }

        return $value;
    }

    /**
     * If post needs a password load the correct template.
     * @param  string $file
     * @return string
     */
    public function maybe_load_password_template($file)
    {
        global $post;

        if (isset($post->ID) && post_password_required($post->ID)) {
            $file = 'password.twig';
        }

        return $file;
    }

    public function add_typography_page()
    {
        $this->create_page_if_null('Typography');
        $this->create_page_if_null('Grid');
    }

    private function create_page_if_null($title)
    {
        if (get_page_by_title($title) == NULL) {
            $page = array(
                'post_title' => $title,
                'post_content' => '',
                'post_status' => 'draft',
                'post_author' => 1,
                'post_type' => 'page',
            );

            wp_insert_post($page);
        }
    }
}
