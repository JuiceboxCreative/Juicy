<?php

namespace JuiceBox\CustomFields;

use Quote\Module as Quote;

class Modules extends CustomField
{
    public static $fields = array(
        'key' => 'group_5667dac192df7',
        'title' => 'Modules',
        'fields' => array(
            array(
                'key' => 'field_5667dace01da6',
                'label' => 'Modules',
                'name' => 'modules',
                'type' => 'flexible_content',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'button_label' => 'Add Layout',
                'min' => '',
                'max' => '',
                'layouts' => array(),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'     => 'post_type',
                    'operator'  => '==',
                    'value'     => 'page',
                )
            ),
            array(
                array(
                    'param'     => 'post_type',
                    'operator'  => '==',
                    'value'     => 'post',
                )
            )
        ),
        'menu_order' => 10,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    );

    public static function register()
    {
        // fields[0] is the layouts array
        static::$fields['fields'][0]['layouts'][] = Quote::$layout;

        acf_add_local_field_group(static::$fields);
    }
}
