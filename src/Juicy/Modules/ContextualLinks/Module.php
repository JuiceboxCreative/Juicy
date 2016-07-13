<?php

namespace Juicy\Modules\ContextualLinks;

use Juicy\Core\Module as JBModule;
use Timber;

class Module extends JBModule
{
    public static $layout = array (
        'key' => 'module_contextual_links',
        'name' => 'contextual_links',
        'label' => 'Contextual Links',
        'instructions' => '',
        'display' => 'block',
        'sub_fields' => array (
            array (
                'key' => 'module_cta_boxes_heading',
                'label' => 'Heading',
                'name' => 'heading',
                'type' => 'text',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
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
            array (
                'key' => 'field_578590865f27a',
                'label' => 'Carousel',
                'name' => 'carousel',
                'type' => 'true_false',
                'instructions' => 'Do you want this to display as a carousel?

    NB: If you choose \'Custom\' for post selection and have a carousel please select at least 6 posts.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'module_cta_boxes_post_selection',
                'label' => 'Post Selection',
                'name' => 'post_selection',
                'type' => 'radio',
                'instructions' => 'Would you like to display a set of chosen contextual links, sibling pages, or a set of randomly chosen contextual links?',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array (
                    'custom' => 'Custom',
                    'sibling' => 'Sibling Pages',
                    'random' => 'Random',
                ),
                'other_choice' => 0,
                'save_other_choice' => 0,
                'default_value' => '',
                'layout' => 'horizontal',
            ),
            array (
                'key' => 'field_578590cc5f27b',
                'label' => 'Items',
                'name' => 'items',
                'type' => 'relationship',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'module_cta_boxes_post_selection',
                            'operator' => '==',
                            'value' => 'custom',
                        ),
                    ),
                ),
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array (
                    0 => 'post',
                    1 => 'page',
                ),
                'taxonomy' => array (
                ),
                'filters' => array (
                    0 => 'search',
                    1 => 'post_type',
                    2 => 'taxonomy',
                ),
                'elements' => '',
                'min' => '',
                'max' => '',
                'return_format' => 'id',
            ),
        ),
    );

    function processModule()
    {
        parent::processModule();

        switch ( $this->module['post_selection'] ) {
            case 'sibling':
                // Get sibling posts
                $siblings = Timber::get_posts(
                    array(
                        'post_parent'       => $this->post->post_parent,
                        'post__not_in'      => array( $this->post->ID ),
                        'posts_per_page'    => 12
                    ),
                    "\\Juicy\\Core\\Post"
                );

                // If we don't have enough post get some random ones
                if ( size($siblings) < 3 ) {
                    $ids = array_map(function( $p ){
                        return $p->ID;
                    }, $siblings);

                    $ids[] = $this->post->ID;

                    $other = Timber::get_posts(
                        array(
                            'post__not_in'      => array( $ids ),
                            'posts_per_page'    => 10,
                            'orderby'           => 'rand',
                            'post_type'         => 'page'
                        ),
                        "\\Juicy\\Core\\Post"
                    );

                    $siblings = array_merge( $siblings, $other );
                }

                if ( $this->module['carousel'] ) {
                    $this->module['items'] = array_rand( $siblings, 9 );
                } else {

                }

                $items = $this->module['carousel'] ? 9 : 3;

                $this->module['items'] = array_rand( $siblings, $items );

                break;
            case 'custom':
                foreach ( $this->module['items'] as $key => $item ) {
                    $this->module['items'][$key] = new Post($item);
                }

                $items = $this->module['carousel'] ? 9 : 3;

                if ( size( $this->module['items'] ) < $items ) {
                    $ids = array_map(function( $p ){
                        return $p->ID;
                    }, $this->module['items']);

                    $ids[] = $this->post->ID;

                    $other = Timber::get_posts(
                        array(
                            'post__not_in'      => array( $ids ),
                            'posts_per_page'    => 10,
                            'orderby'           => 'rand',
                            'post_type'         => 'page'
                        ),
                        "\\Juicy\\Core\\Post"
                    );

                    $this->module['items'] = array_merge( $this->module['items'], $other );
                }

                $this->module['items'] = array_splice($this->module['items'], 0, $items);

                break;

            case 'random':
                $this->module['items'] = Timber::get_posts(
                    array(
                        'post__not_in'      => array( $this->post->ID ),
                        'posts_per_page'    => $this->module['carousel'] ? 9 : 3,
                        'orderby'           => 'rand',
                        'post_type'         => 'page'
                    ),
                    "\\Juicy\\Core\\Post"
                );
                break;
        }
    }
}
