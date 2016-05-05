<?php

namespace Juicy\CustomFields;

class CustomField
{
    public static function register()
    {
        if ( function_exists('acf_add_local_field_group') ) {
            acf_add_local_field_group(static::$fields);
        }
    }
}
