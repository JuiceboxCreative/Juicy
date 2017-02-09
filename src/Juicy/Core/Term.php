<?php

namespace Juicy\Core;

use Timber;
use TimberTerm;

class Term extends TimberTerm {

    public $PostClass = '\\Juicy\\Core\\Post';
    public $TermClass = '\\Juicy\\Core\\Term';
    public $ImageClass = '\\Juicy\\Core\\Image';

    public function title()
    {
        return $this->name;
    }

    public function content()
    {
        return $this->description;
    }

    public function post_type()
    {
        return $this->object_type;
    }

    public function get_taxonomy()
    {
        return get_taxonomy( $this->taxonomy );
    }

    public function get_field($selector)
    {
        return get_field($selector, "{$this->taxonomy}_{$this->term_id}");
    }

    public function get_thumbnail($fallback = false)
    {
        $img = $this->get_field('featured_image');

        if ( $img !== null ) {
            return new $this->ImageClass($img);
        }

        if( $fallback ) {
            $default = get_field($fallback, 'option');

            if ( ! empty($default) ) {
                return new $this->ImageClass($default);
            }
        }

        return null;
    }

    /**
     * @return null|TimberImage
     */
    public function thumbnail($fallback = false)
    {
        return $this->get_thumbnail($fallback);
    }

    public function get_modules()
    {
        $modules = $this->get_field('modules');

        if ( empty($modules) ) {
            return;
        }

        $processedModules = array();

        foreach ($modules as $index => $module) {
            $name = $module['acf_fc_layout'];

            // Module processor namespace is PascalCase. Convert from underscore name in ACF
            $parts = explode('_', $name);
            $parts = array_map(function ($word) {
                return ucfirst($word);
            }, $parts);
            $namespace = implode('', $parts);
            $fqcn = '\\Juicy\\Modules\\' . $namespace.'\\Module';
            $fqcn = class_exists($fqcn) ? $fqcn : '\\JuiceBox\\Modules\\'.$namespace.'\\Module';

            $moduleProcessor = new $fqcn($module, $name, $this);
            $module = $moduleProcessor->getModule();

            $module['template'] = $moduleProcessor->getTemplate();
            $module['fqcn'] = $fqcn;
            $module['index'] = $index;
            $module['name'] = $name;

            $processedModules[] = $module;
        }

        return $processedModules;
    }
}
