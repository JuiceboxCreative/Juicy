<?php

namespace JuiceBox\PostType;

class CustomPostType
{
    protected static $postType = 'custom_post_type';

    protected static $postName = 'Custom Post Type';

    protected static $singularName = 'Custom Post Type';

    protected static $pluralName = 'Custom Post Types';

    protected static $supports = array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'excerpt'
    );

    public static function register()
    {
        register_post_type( static::postType(), [
            'label'               => __( static::postName(), 'text_domain' ),
            'labels'              => array(
                'name'                => _x( static::pluralName(), 'Post Type General Name', 'text_domain' ),
                'singular_name'       => _x( static::singularName(), 'Post Type Singular Name', 'text_domain' ),
                'menu_name'           => __( static::pluralName(), 'text_domain' ),
                'name_admin_bar'      => __( static::pluralName(), 'text_domain' ),
                'add_new_item'        => __( 'Add New ' . static::singularName(), 'text_domain' ),
                'add_new'             => __( 'Add New ' . static::singularName(), 'text_domain' ),
                'new_item'            => __( 'New ' . static::singularName(), 'text_domain' ),
                'edit_item'           => __( 'Edit ' . static::singularName(), 'text_domain' ),
                'update_item'         => __( 'Update ' . static::singularName(), 'text_domain' ),
                'view_item'           => __( 'View ' . static::singularName(), 'text_domain' ),
                'search_items'        => __( 'Search ' . static::pluralName(), 'text_domain' ),
            ),
            'supports'            => static::supports(),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'menu_icon'           => 'dashicons-index-card'
        ] );
    }

    public static function postType()
    {
        return static::$postType;
    }

    public static function postName()
    {
        return static::$postName;
    }

    public static function singularName()
    {
        return static::$singularName;
    }

    public static function pluralName()
    {
        return static::$pluralName;
    }

    public static function supports()
    {
        return static::$supports;
    }
}
