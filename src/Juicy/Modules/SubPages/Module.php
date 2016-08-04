<?php

namespace Juicy\Modules\SubPages;

use Juicy\Core\Module as JBModule;
use Timber;

class Module extends JBModule
{
    public static $layout = array (
        'key' => 'module_sub_pages',
        'name' => 'sub_pages',
        'label' => 'Sub Pages',
        'display' => 'block',
        'sub_fields' => array (
            array (
                'key' => 'field_5733f323141234ea',
                'label' => 'Note',
                'name' => 'note',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => 'This module will display all child pages of this page, as defined in the <a href="/wp-admin/nav-menus.php?action=edit&menu=3">Main Menu</a>.',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            )
        ),
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

        $context = Timber::get_context();

        $this->crawlMenu($context['menus']['main_menu']->items, $this->post->ID);
    }

    private function crawlMenu( $menu, $id )
    {
        foreach ( $menu as $items ) {
            $this->crawlMenuItem($items, $id);
        }
    }

    private function crawlMenuItem( $item, $id )
    {
        // Handle top level items
        if ( $item->object_id == $id && isset($item->children) && $item->children !== array() ) {
            $this->formatChildren($item->children);
        }

        if ( isset($item->children) && $item->children !== array() ) {
            foreach ( $item->children as $child ) {
                $this->crawlMenuItem($child, $id);
            }
        }
    }

    private function formatChildren( $children )
    {
        $this->module['pages'] = array();

        foreach ( $children as $child ) {
            switch ( $child->type ) {
                case 'taxonomy':
                    $this->module['pages'][] = new \JuiceBox\Core\Term($child->_menu_item_object_id);
                    break;
                case 'post_type_archive':
                    $options = getOptionByPrefix($child->object);

                    $this->module['pages'][] = array(
                        'link'      => $child->url,
                        'title'     => $child->name,
                        'post_type' => 'page',
                        'thumbnail' => get_field('placeholder_image', 'option'),
                    );
                    break;
                default:
                    $this->module['pages'][] = new \JuiceBox\Core\Post($child->_menu_item_object_id);
                    break;
            }
        }
    }
}
