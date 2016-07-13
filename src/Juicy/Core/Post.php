<?php

namespace Juicy\Core;

use TimberPost;

class Post extends TimberPost
{
    public $PostClass = '\\Juicy\\Core\\Post';
    public $ImageClass = '\\Juicy\\Core\\Image';
    public $subnav = null;
    public $modules = null;

    public function get_subnav()
    {
        if ($this->subnav !== null) {
            return $this->subnav;
        }

        $this->subnav = new Subnav($this);

        return $this->subnav;
    }

    public function get_thumbnail($fallback = false)
    {
        $tid = get_post_thumbnail_id($this->ID);

        if ( $tid ) {
            return new $this->ImageClass((int) $tid);
        }

        if ($fallback) {
            $default = get_field('placeholder_image', 'option');

            if (! empty($default)) {
                return new $this->ImageClass($default);
            }
        }

        return null;
    }

    public function thumbnail($fallback = false)
    {
        return $this->get_thumbnail($fallback);
    }

    public function get_fb_share_link()
    {
        return 'http://www.facebook.com/sharer/sharer.php?u='.$this->permalink;
    }

    public function get_twitter_share_link()
    {
        return 'http://twitter.com/share?text='.urlencode(html_entity_decode($this->title . " - ". get_bloginfo('name'), ENT_COMPAT, 'UTF-8'))."&amp;url=" . get_bloginfo('url') . "?p=" . $this->ID;
    }

    public function get_modules()
    {
        $modules = get_field('modules', $this->ID);

        if (empty($modules)) {
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
            $fqcn = '\\'.$namespace.'\\Module';

            $moduleProcessor = new $fqcn($module, $name, $this);
            $module = $moduleProcessor->getModule();

            $module['template'] = $moduleProcessor->getTemplate();
            $module['fqcn'] = $fqcn;
            $module['index'] = $index;
            $module['name'] = $name;
            $module['key'] = $fqcn::$layout['key'];

            $processedModules[] = $module;
        }

        return $processedModules;
    }

    public function has_children() {
        $children = $this->get_children();
        return (is_array($children) && count($children) > 0);
    }
}
