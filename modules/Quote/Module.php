<?php

namespace Quote;

use JuiceBox\Core\Module as JBModule;
use Timber;

class Module extends JBModule
{
    public static $layout = array(
        'key' => 'module_jb_quote1',
        'name' => 'quote',
        'label' => 'Quote',
        'display' => 'block',
        'sub_fields' => array(
            array(
                'key' => 'module_0_subfield_0',
                'label' => 'Quote',
                'name' => 'quote',
                'type' => 'textarea',
                'new_lines' => 'br',
                'rows' => 4
            ),
            array(
                'key' => 'module_0_subfield_1',
                'label' => 'Padding',
                'instructions' => 'Include padding to the top or bottom of the module.',
                'name' => 'padding',
                'type' => 'checkbox',
                'layout' => 'horizontal',
                'choices' => array(
                    'padding-top' => 'Padding Top',
                    'padding-bottom' => 'Padding Bottom'
                )
            )
        )
    );
}
