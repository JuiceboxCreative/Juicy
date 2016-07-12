<?php

namespace Juicy\Config;

class Assets
{
    public static $css = array();

    public static $js = array();

    public static $jsData = array();

    /**
     * En-queue required assets
     *
     * @param  string  $filter   The name of the filter to hook into
     * @param  integer $priority The priority to attach the filter with
     */
    public static function load($filter = 'wp_enqueue_scripts', $priority = 10)
    {
        // Register the filter
        add_filter($filter, function () {

            // CSS
            if ( static::$css !== array() ) {
                foreach ( static::$css as $css ) {
                    wp_enqueue_style( $css['handle'] , $css['src'], $css['deps'], filemtime($css['src']));
                }
            }

            // JS
            if ( static::$js !== array() ) {
                foreach ( static::$js as $js ) {
                    wp_enqueue_script( $js['handle'] , $js['src'], $js['deps'], filemtime($js['src']), $js['in_footer']);
                }
            }

            // JS data
            if ( static::$jsData !== array() ) {
                foreach ( static::$jsData as $jsData ) {
                    wp_localize_script($jsData['handle'], $jsData['name'], $jsData['data']);

                    wp_enqueue_script($jsData['handle']);
                }
            }
        }, $priority);
    }
}
