<?php

namespace Juicy\Core;

abstract class Module
{
    protected $module = null;
    protected $name;
    protected $post;

    /**
     * Returns processed module
     *
     * @param array $module
     */
    public function __construct($module, $name, $post)
    {
        $this->setModule($module);
        $this->setName($name);
        $this->setPost($post);
        $this->processModule();
    }

    /**
     * Set Module
     *
     * @param array $module
     * @return \Juicy\Core\Module
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Set Name
     *
     * @param array $name
     * @return \Juicy\Core\Module
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
     * @return \Juicy\Core\Module
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
     * Does any processing for this module.
     */
    public function processModule()
    {

    }

    /**
     * To String
     */
    public function _toString()
    {
        $context = Timber::get_context();

        $context['module'] = $this->getModule();

        return Timber::compile($this->getNamespace() . "/template.twig", $context);
    }

    protected function getNamespace()
    {
        $reflector = new \ReflectionClass($this);

        return $reflector->getNamespaceName();
    }
}
