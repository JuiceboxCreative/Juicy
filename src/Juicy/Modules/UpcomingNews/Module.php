<?php

namespace Juicy\Modules\UpcomingNews;

use Juicy\Core\Module as JBModule;
use Timber;

class Module extends JBModule
{
    protected $jsDependencies = array('jquery', 'jb_script');

    public static $layout = array (
        'key' => 'module_news_carousel',
        'name' => 'upcoming_news',
        'label' => 'Upcoming News',
        'display' => 'block',
        'sub_fields' => array (
            array (
                'key' => 'module_news_carousel_heading',
                'label' => 'Heading',
                'name' => 'heading',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'News',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'module_news_carousel_see_more_text',
                'label' => 'Link',
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
            array (
                'key' => 'field_574d0d02792e7',
                'label' => 'How would you like to select the Categories?',
                'name' => 'category_select_type',
                'type' => 'radio',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array (
                    'all' => 'All Categories',
                    'include' => 'Include Specific Categories',
                    'exclude' => 'Exclude Specific Categories',
                ),
                'allow_null' => 0,
                'other_choice' => 0,
                'save_other_choice' => 0,
                'default_value' => '',
                'layout' => 'horizontal',
            ),
            array (
                'key' => 'field_574d0d35792e8',
                'label' => 'Include Categories',
                'name' => 'include_categories',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_574d0d02792e7',
                            'operator' => '==',
                            'value' => 'include',
                        ),
                    ),
                ),
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'category',
                'field_type' => 'checkbox',
                'allow_null' => 0,
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 0,
            ),
            array (
                'key' => 'field_574d0d5a792e9',
                'label' => 'Exclude Categories',
                'name' => 'exclude_categories',
                'type' => 'taxonomy',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_574d0d02792e7',
                            'operator' => '==',
                            'value' => 'exclude',
                        ),
                    ),
                ),
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'taxonomy' => 'category',
                'field_type' => 'checkbox',
                'allow_null' => 0,
                'add_term' => 0,
                'save_terms' => 0,
                'load_terms' => 0,
                'return_format' => 'id',
                'multiple' => 0,
            ),
        ),
    );

    function processModule()
    {
        parent::processModule();

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'post__not_in'  => array($this->post->ID)
        );

        $category_select_type = $this->module['category_select_type'];

        if ( $category_select_type == 'include' ) {
            $args['category__in'] = $this->module['include_categories'];
        } elseif ( $category_select_type == 'exclude' ) {
            $args['category__not_in'] = $this->module['exclude_categories'];
        }

        $this->module['posts'] = Timber::get_posts($args, '\\Juicy\\Core\\Post');

        $this->module['posts'] = array_splice($this->module['posts'], 0, 3);

    }
}
