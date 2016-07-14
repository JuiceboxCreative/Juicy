<?php

namespace Juicy\Core;

abstract class Component extends Module
{
    protected $component = null;
    protected $jsDependencies = array('jquery');
    protected $cssDependencies = array();
    protected $name;
    protected $post;

    /**
     * Returns processed component
     *
     * @param array $component
     */
    public function __construct($component, $name, $post)
    {
        $this->setComponent($component);
        $this->setName($name);
        $this->setPost($post);
        $this->processComponent();
    }

    /**
     * Set Component
     *
     * @param array $component
     * @return Juicy\Component
     */
    public function setComponent($component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get Component
     *
     * @return array
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Does any processing for this component
     *
     * @param  array $component
     * @return array
     */
    public function processComponent()
    {
        $this->addAssets();
    }

    /**
     * To String
     * @return [type] [description]
     */
    public function _toString()
    {
        $context = Timber::get_context();

        $context['component'] = $this->getComponent();

        $this->processComponent();

        return Timber::compile($this->getNamespace() . "/template.twig", $context);
    }

    protected function addAssets()
    {
        $name = 'component_'.implode('_', explode(' ', strtolower($this->name)));
        $themeDir = get_template_directory();
        $themeDirUri = get_template_directory_uri();
        $componentPath = $themeDir.'/src/'.$this->getNamespace().'/';
        $componentPathUri = $themeDirUri.'/src/'.$this->getNamespace().'/';

        $componentPath = str_replace( "\\", "/", $componentPath );
        $componentPathUri = str_replace( "\\", "/", $componentPathUri );

        if ( file_exists( $componentPath.'javascript.js' ) ) {
            wp_enqueue_script($name, $componentPathUri.'javascript.js', $this->jsDependencies, '0.0.1', true);
        }
    }

    public static function register()
    {
        if ( function_exists('acf_add_local_field_group') ) {
            acf_add_local_field_group(static::$fields);
        }
    }
}
