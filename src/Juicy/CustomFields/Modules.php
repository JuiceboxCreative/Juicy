<?php

namespace Juicy\CustomFields;

class Modules extends CustomField
{
    public static $excludeModules = array();

    public static $fields = array(
        'key' => 'group_modules',
        'title' => 'Modules',
        'fields' => array(
            array(
                'key' => 'group_modules_field_modules',
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
                'button_label' => 'Add Module',
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

    public function register() {
        
    }
}
