<?php

namespace Juicy\Config;

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

        add_shortcode('theme_option', array(__CLASS__, 'theme_option'));
    }

    public static function theme_option( $atts )
    {
        $atts = shortcode_atts([
            'value' => ''
        ], $atts);

        if ( $atts['value'] == '' ) {
            return new \WP_Error('Missing Value', 'Please Specify a value for this shortcode.', $atts);
        }

        if ( $atts['value'] == 'client_name' ) {
            $option = get_option('blogname');
        } else {
            $option = get_field($atts['value'], 'option');
        }

        if ( empty($option) ) {
            return new \WP_Error('Empty Field', "The value returned from your specified option (`{$atts['value']}`) is either empty or doesn't exist.", $atts);
        }

        return $option;
    }

    public static function html_sitemap()
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
                ), "\\Juicy\\Core\\Post")
            );
        }

        $context = array(
            'sitemap' => $sections
        );

        return Timber::compile('partials/sitemap.twig', $context);
    }

    public function dummy_content()
    {
        return Timber::compile('partials/dummy-content.twig');
    }
}
