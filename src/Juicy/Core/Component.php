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
     * @return Juicy\Core\Component
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

    public static function register()
    {
        if ( function_exists('acf_add_local_field_group') ) {
            acf_add_local_field_group(static::$fields);
        }
    }
}
