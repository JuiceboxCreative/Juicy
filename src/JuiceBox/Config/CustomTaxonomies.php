<?php

namespace JuiceBox\Config;

use JuiceBox\Taxonomy\Example;

class CustomTaxonomies
{
    public static function register($filter = 'init', $priority = 10)
    {
        add_action($filter, function(){
            Example::register();
        }, $priority);
    }
}
