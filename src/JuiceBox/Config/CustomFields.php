<?php

namespace JuiceBox\Config;

use JuiceBox\CustomFields\Modules;

class CustomFields
{
    public static function register($filter = 'acf/init', $priority = 10)
    {
        add_action($filter, function(){
            Modules::register();
        }, $priority);
    }
}
