<?php

namespace Juicy\Core;

trait HasModuleLoop {

    public function get_modules()
    {
        $modules = $this->get_field('modules');

        if (empty($modules)) {
            return;
        }

        $processedModules = array();

        foreach ($modules as $index => $module) {
            if (!isset($module['acf_fc_layout'])) {
                var_dump($module);
                throw new \Exception('Module is missing the acf_fc_layout key.');
            }

            $name = $module['acf_fc_layout'];

            // Module processor namespace is PascalCase. Convert from underscore name in ACF
            $parts = explode('_', $name);
            $parts = array_map(function ($word) {
                return ucfirst($word);
            }, $parts);
            $namespace = implode('', $parts);
            $fqcn = '\\JuiceBox\\Modules\\'.$namespace.'\\Module';

            $moduleProcessor = new $fqcn($module, $name, $this);
            $module = $moduleProcessor->getModule();

            $module['template'] = $moduleProcessor->getTemplate();
            $module['fqcn'] = $fqcn;
            $module['index'] = $index;
            $module['name'] = $name;

            $processedModules[] = $module;
        }

        return $processedModules;
    }
}
