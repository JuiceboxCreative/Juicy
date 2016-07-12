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
     * Adds modules to the loop. Handles the excludion of modules.
     * @param String $filename The module namespace
     * @return boolean true|false Returns false if the module was excluded, true if it was added
     */
    private static function addComponent( $filename )
    {
        if ( in_array( $filename, static::$excludeComponents ) ) {
            return false;
        }

        $class = "\\JuiceBox\\Components\\{$filename}\\Components";

        $class::register();

        return true;
    }
}
