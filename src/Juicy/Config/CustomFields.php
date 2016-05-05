<?php

namespace Juicy\Config;

use Juicy\CustomFields\Modules;

class CustomFields
{
    public static function register($filter = 'acf/init', $priority = 10)
    {
        add_action($filter, function(){
            Modules::register();
        }, $priority);
    }
}
