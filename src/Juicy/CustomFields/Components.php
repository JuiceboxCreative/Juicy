<?php

namespace Juicy\CustomFields;

class Components
{
    public static $excludeComponents = array();

    public static function register()
    {
        $dir = new \DirectoryIterator(get_template_directory() . '/src/Juicy/Components');
        foreach ($dir as $dirinfo) {
            if (!$dirinfo->isDot() && $dir->isDir()) {
                static::addComponent( $dirinfo->getFilename() );
            }
        }
    }

    /**
     * Adds components to the loop. Handles the exclusion of components.
     * @param String $filename The component namespace
     * @return boolean true|false Returns false if the component was excluded, true if it was added
     */
    private static function addComponent( $filename )
    {
        if ( in_array( $filename, static::$excludeComponents ) ) {
            return false;
        }

        $class = "\\Juicy\\Components\\{$filename}\\Component";

        $class::register();

        return true;
    }
}
