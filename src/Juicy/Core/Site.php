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
        add_action('init', array($this, 'add_default_pages'));

        // Prevent iconbox files from going to S3.
        add_filter('as3cf_pre_update_attachment_metadata', array($this, 'pre_update_attachment_metadata'), 10, 3);

        add_action('after_setup_theme', array($this, 'schema_breadcrumbs'));

        add_filter('acf/format_value/type=post_object', array($this, 'field_to_jb_post'), 99, 3);

        add_filter('acf/format_value/type=relationship', array($this, 'field_to_jb_post'), 99, 3);

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

        // Add responsive wrapper around oEmbed elements and tables
        add_filter('embed_oembed_html', [$this, 'wrap_embed'], 10, 1);
        add_filter('the_content', array($this, 'add_div_to_tables'), 99);

        // Error fix
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10, 0 );

        // Move 'Yoast' to the bottom of the page
        add_filter( 'wpseo_metabox_prio', function(){
            return 'low';
        });

        // prevent robots crawling dev domains.
        add_filter('robots_txt', [$this, 'dev_robots_disallow'], 10, 2);

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
            if ( !is_array($value) ) {
                return new $this->PostClass($value);
            } else {
                foreach ( $value as &$val ) {
                    $val = new $this->PostClass($val);
                }
            }
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

    public function add_default_pages()
    {
        $this->create_page_if_null('Typography');
        $this->create_page_if_null('Grid');
        $this->create_page_if_null('Home Page', 'publish', 'page--home-page.php');
        $this->create_page_if_null('Contact', 'publish', 'page--contact.php');
    }

    public function create_page_if_null($title, $status = 'draft', $template = null)
    {
        if (get_page_by_title($title) == NULL) {
            $page = array(
                'post_title' => $title,
                'post_content' => '',
                'post_status' => $status,
                'post_author' => 1,
                'post_type' => 'page',
                'post_name' => strtolower(str_replace(' ', '-', $title)),
            );

            if ( $template ) {
                $page['page_template'] = $template;
            }

            wp_insert_post($page);
        }
    }

    /**
     * Filter for adding wrappers around oEmbeds
     */
    public function wrap_embed($html)
    {
        $html = preg_replace('/(width|height)="\d*"\s/', "", $html); // Strip width and height #1

        return '<div class="embed-responsive">' . $html . '</div>'; // Wrap in div element and return #3 and #4
    }

    public function add_div_to_tables($content)
    {
        $replace = array(
            '<table' => '<div class="table-responsive"><table',
            '</table>' => '</table></div>'
        );

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $content
        );
    }

    public function dev_robots_disallow( $output, $public ) {
        if (preg_match('/.+\.box$/', $_SERVER['HTTP_HOST'])
            || preg_match('/.+\.dev.juicebox.com.au$/', $_SERVER['HTTP_HOST'])
            || preg_match('/.+\.cloudsites.net.au$/', $_SERVER['HTTP_HOST'])) {
            $output = "User-agent: *\nDisallow: /";
        }

        return $output;
    }
}
