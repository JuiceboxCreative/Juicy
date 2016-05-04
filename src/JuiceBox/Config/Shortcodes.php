<?php

namespace JuiceBox\Config;

use Timber;

/**
 * Add custom short codes
 */
class Shortcodes
{
    public static function register()
    {
        add_shortcode('html-sitemap', array(__CLASS__, 'html_sitemap'));

        add_shortcode('site', array(__CLASS__, 'site'));

        add_shortcode('dummy-content', array(__CLASS__, 'dummy_content'));
    }

    public function html_sitemap()
    {
        $sections = array();

        foreach( get_post_types( array('public' => true) ) as $post_type ) {
            if ( in_array( $post_type, array('attachment') ) )
                continue;

            $pt = get_post_type_object( $post_type );

            $sections[] = array(
                'title' => $pt->labels->name,
                'items' => Timber::get_posts(array(
                    'post_type' => $post_type,
                    'posts_per_page' => -1,
                    'sort_column' => 'menu_order'
                ), "\\JuiceBox\\Core\\Post")
            );
        }

        $context = array(
            'sitemap' => $sections
        );

        return Timber::compile('partials/sitemap.twig', $context);
    }

    public function site($atts)
    {
        $atts = shortcode_atts(array(
            'data' => 'name'
        ), $atts, 'site');

        //Try custom field first
        $string = get_field($atts['data'], 'option');

        //If custom field doesn't exist try get site info
        if(empty($string)) {
            $context = Timber::get_context();
            $string = $context['site']->{$atts['data']};
        }

        return $string;
    }

    public function dummy_content()
    {
        return Timber::compile('partials/dummy-content.twig');
    }
}
