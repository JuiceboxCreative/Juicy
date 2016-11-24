<?php
/**
 * A class of helper functions that can be reused across sites.
 */

namespace Juicy\Core;

class Helpers
{
    /**
     * Are we in dev?
     * @return boolean
     */
    public static function is_dev()
    {
        return (defined('ENV') && ENV === 'development');
    }

    /**
     * Get site options by a prefix
     * @param  string $prefix the prefix of the option, e.g. error
     * @return array         An associative array of option, with the prefix removed
     */
    public static function field_by_prefix( $prefix, $field = 'option' )
    {
        // Get all options
        $fields = get_fields($field);
        // Init array
        $return = array();
        // Loop through each option, we will test the key against the prefix passed in
        foreach ( $fields as $key => $option ) {
            // If the first X letters of the key are equal to the prefix we have an option
            if ( substr($key, 0, strlen($prefix)) === $prefix ) {
                // remove the prefix, we don't need it anymore
                $return[str_replace($prefix.'_', '', $key)] = $option;
            }

        }

        return $return;
    }

    public static string_exists( $needle, $haystack )
    {
        return (strpos($haystack, $needle) !== false);
    }
}
