<?php

namespace Juicy\Components\HomePageBanner;

use Juicy\Core\Component as Component;
use Timber;

class HomePageBanner extends Component
{
    public static $fields = array(
        'key' => 'group_572ab881bdc50',
        'title' => 'Home Page Banner',
        'fields' => array(
            array(
                'key' => 'field_572ab8a215a7c',
                'label' => 'Banner',
                'name' => 'banner',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 1,
                'max' => '',
                'layout' => 'block',
                'button_label' => 'Add Slide',
                'sub_fields' => array(
                    array(
                        'key' => 'field_572ab8e315a7d',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => 50,
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                        'readonly' => 0,
                        'disabled' => 0,
                    ),
                    array(
                        'key' => 'field_572ab9607f0jfnajkasda2',
                        'label' => 'Call to action',
                        'name' => 'link',
                        'type' => 'link',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => 50,
                            'class' => '',
                            'id' => '',
                        ),
                        'show_fields' => array(
                            0 => 'title',
                            1 => 'target',
                        ),
                    ),
                    array(
                        'key' => 'field_572ab90715a7f',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'id',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page--home.php',
                ),
            ),
        ),
        'menu_order' => 5,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => 1,
        'description' => '',
    );

    /**
     * Does any processing for this module
     *
     * @param  array $module
     * @return array
     */
    public function processModule()
    {
        parent::processModule();

        foreach ( $this->module as $key => $slide ) {
            $slide['image'] = new \Juicy\Core\Image($slide['image']);
            $this->module[$key] = $slide;
        }
    }

}
