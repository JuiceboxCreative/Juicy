<?php

namespace Juicy\Core;

abstract class Module
{
    protected $module = null;
    protected $jsDependencies = array('jquery');
    protected $name;

    /**
     * Returns processed module
     *
     * @param array $module
     */
    public function __construct($module)
    {
        $this->setModule($module);
        $this->processModule();
    }

    /**
     * Set Module
     *
     * @param array $module
     * @return Juicy\Module
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get Module
     *
     * @return array
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Does any processing for this module
     *
     * @param  array $module
     * @return array
     */
    public function processModule()
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

        $context['module'] = $this->getModule();

        return Timber::compile($this->getNamespace() . "/template.twig", $context);
    }

    protected function addAssets()
    {
        $name = 'module_'.implode('_', explode(' ', strtolower($this->name)));
        $themeDir = get_template_directory_uri();
        $modulePath = $themeDir.'/modules/'.$this->getNamespace().'/';

        wp_enqueue_script($name, $modulePath.'javascript.js', $this->jsDependencies, '0.0.1', true);
    }

    private function getNamespace()
    {
        $reflector = new \ReflectionClass($this);

        return $reflector->getNamespaceName();
    }
}
