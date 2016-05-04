<?php

namespace JuiceBox\Config;

use JuiceBox\PostType\Example;

class CustomPostTypes
{
    public static function register($filter = 'init', $priority = 10)
    {
        add_action($filter, function(){
            Example::register();
        }, $priority);
    }
}
