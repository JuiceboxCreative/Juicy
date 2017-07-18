<?php

namespace Juicy\Core;

class Twig extends \Timber\Twig {

    public function __construct()
    {
        // Add custom filters to twig.
        add_action('timber/twig/filters', array($this, 'add_timber_filters'));
    }

    /**
     * Register the Timber filters, except for the image filters.
     *
     * @param \Timber\Twig_Environment $twig
     * @return mixed|\Timber\Twig_Environment|void
     */
    public function add_timber_filters( $twig ) {
        $twig->addFilter(new \Twig_SimpleFilter('resize', array('Juicy\Core\ImageHelper', 'resize')));

        /* debugging filters */
        $twig->addFilter(new \Twig_SimpleFilter('get_class', 'get_class'));
        $twig->addFilter(new \Twig_SimpleFilter('get_type', 'get_type'));
        $twig->addFilter(new \Twig_SimpleFilter('print_r', function( $arr ) {
            return print_r($arr, true);
        } ));

        /* other filters */
        $twig->addFilter(new \Twig_SimpleFilter('stripshortcodes', 'strip_shortcodes'));
        $twig->addFilter(new \Twig_SimpleFilter('array', array($this, 'to_array')));
        $twig->addFilter(new \Twig_SimpleFilter('excerpt', 'wp_trim_words'));
        $twig->addFilter(new \Twig_SimpleFilter('excerpt_chars', array('Timber\TextHelper','trim_characters')));
        $twig->addFilter(new \Twig_SimpleFilter('function', array($this, 'exec_function')));
        $twig->addFilter(new \Twig_SimpleFilter('pretags', array($this, 'twig_pretags')));
        $twig->addFilter(new \Twig_SimpleFilter('sanitize', 'sanitize_title'));
        $twig->addFilter(new \Twig_SimpleFilter('shortcodes', 'do_shortcode'));
        $twig->addFilter(new \Twig_SimpleFilter('time_ago', array($this, 'time_ago')));
        $twig->addFilter(new \Twig_SimpleFilter('wpautop', 'wpautop'));
        $twig->addFilter(new \Twig_SimpleFilter('list', array($this, 'add_list_separators')));

        $twig->addFilter(new \Twig_SimpleFilter('pluck', array('Timber\Helper', 'pluck')));

        $twig->addFilter(new \Twig_SimpleFilter('relative', function( $link ) {
            return URLHelper::get_rel_url($link, true);
        } ));

        $twig->addFilter(new \Twig_SimpleFilter('date', array($this, 'intl_date')));

        $twig->addFilter(new \Twig_SimpleFilter('truncate', function( $text, $len ) {
            return TextHelper::trim_words($text, $len);
        } ));

        /* actions and filters */
        $twig->addFilter(new \Twig_SimpleFilter('apply_filters', function() {
            $args = func_get_args();
            $tag = current(array_splice($args, 1, 1));

            return apply_filters_ref_array($tag, $args);
        } ));


        $twig = apply_filters('timber/twig', $twig);
        /**
         * get_twig is deprecated, use timber/twig
         */
        $twig = apply_filters('get_twig', $twig);

        return $twig;
    }
}
