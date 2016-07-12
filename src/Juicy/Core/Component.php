<?php

namespace Juicy\Core;

abstract class Component
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
     * Set Name
     *
     * @param array $name
     * @return Juicy\Name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name
     *
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Post
     *
     * @param array $post
     * @return Juicy\Post
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get Post
     *
     * @return array
     */
    public function getPost()
    {
        return $this->post;
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
        $themeDir = get_template_directory_uri();
        $componentPath = $themeDir.'/src/Juicy/Components/'.$this->getNamespace().'/';

        if ( file_exists( $componentPath.'javascript.js' ) ) {
            wp_enqueue_script($name, $componentPath.'javascript.js', $this->jsDependencies, '0.0.1', true);
        }

        if ( file_exists( $componentPath.'style.css' ) ) {
            wp_enqueue_style($name, $componentPath.'style.css', $this->cssDependencies, '0.0.1');
        }
    }

    protected function getNamespace()
    {
        $reflector = new \ReflectionClass($this);

        return $reflector->getNamespaceName();
    }

    public static function register()
    {
        if ( function_exists('acf_add_local_field_group') ) {
            acf_add_local_field_group(static::$fields);
        }
    }
}
