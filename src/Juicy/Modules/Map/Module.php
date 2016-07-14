<?php

namespace Juicy\Modules\Map;

use Juicy\Core\Module as JBModule;
use Timber;

class Module extends JBModule
{
    public static $layout = array(
        'key' => 'module_map',
        'name' => 'map',
        'label' => 'Map',
        'instructions' => '',
        'display' => 'block',
        'sub_fields' => array(
            array(
                'key' => 'module_map1239scvn922345-124',
                'label' => 'Map',
                'name' => 'map',
                'type' => 'google_map',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
            ),
        ),
    );

    public function processModule()
    {
        wp_enqueue_script('jb_map', "//maps.googleapis.com/maps/api/js?key=AIzaSyDBkfJltZseT_Mjjm4kTFBrAw2LKN-s5gk");

        $this->jsDependencies[] = 'jb_map';

        parent::processModule();

        wp_localize_script($this->name, 'moduleData', array(
            'childThemeDir' => get_stylesheet_directory_uri()
        ));

        wp_enqueue_script($this->name);
    }
}
