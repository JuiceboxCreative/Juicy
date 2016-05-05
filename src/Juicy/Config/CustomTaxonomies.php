<?php

namespace Juicy\Config;

use Juicy\Taxonomy\Example;

class CustomTaxonomies
{
    public static function register($filter = 'init', $priority = 10)
    {
        add_action($filter, function(){
            Example::register();
        }, $priority);
    }
}
