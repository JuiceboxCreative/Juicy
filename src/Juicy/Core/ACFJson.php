<?php

namespace Juicy\Core;

class ACFJson
{
    protected $json_location;
    protected $module_location;
    protected $module_key;

    public function __construct()
    {
        // Stop ACF handling JSON, we are taking over
        add_filter('acf/settings/json', '__return_false');

        // Set the location for json to live, your child theme can override.
        $this->json_location = apply_filters('jb/acfjson/json_location', get_stylesheet_directory() . '/acf-json');
        $this->module_location = apply_filters('jb/acfjson/module_location', get_stylesheet_directory() . '/src/JuiceBox/Modules/');

        add_action('acf/update_field_group',        [$this, 'update_field_group'], 10, 5);
        add_action('acf/duplicate_field_group',     [$this, 'update_field_group'], 10, 5);
        add_action('acf/untrash_field_group',       [$this, 'update_field_group'], 10, 5);
        add_action('acf/trash_field_group',         [$this, 'delete_field_group'], 10, 5);
        add_action('acf/delete_field_group',        [$this, 'delete_field_group'], 10, 5);
        add_action('acf/include_fields',            [$this, 'include_fields'], 10, 5);
    }

    public function update_field_group( $field_group )
    {
        // get fields
        $field_group['fields'] = acf_get_fields( $field_group );

        // save file
        $this->acf_write_json_field_group( $field_group );
    }

    public function delete_field_group( $field_group )
    {
        // WP appends '__trashed' to end of 'key' (post_name)
        $field_group['key'] = str_replace('__trashed', '', $field_group['key']);

        // delete
        acf_delete_json_field_group( $field_group['key'] );
    }

    public function include_fields()
    {
        // vars
        $path = $this->json_location;

        // remove trailing slash
        $path = untrailingslashit( $path );

        // check that path exists
        if( !file_exists( $path ) ) {
            return;
        }

        $dir = opendir( $path );

        while(false !== ( $file = readdir($dir)) ) {

            // only json files
            if( strpos($file, '.json') === false ) {
                continue;
            }

            // read json
            $json = file_get_contents("{$path}/{$file}");

            // validate json
            if( empty($json) ) {
                continue;
            }

            // decode
            $json = json_decode($json, true);

            // Load module fields
            if ( $json['key'] == $this->module_key ) {
                $json['fields'][0]['layouts'] = $this->include_module_fields();
            }

            // add local
            $json['local'] = 'json';

            // add field group
            acf_add_local_field_group( $json );

        }
    }

    public function admin_notice_error__acf_folder()
    {
        printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', 'ACF JSON folder is not writable.' );
    }

    public function admin_notice_error__layout_name()
    {
        printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', 'You have not specified a name for one of your modules.' );
    }

    public function admin_notice_error__module_folder()
    {
        printf( '<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', 'One of your modules folder strucutre or permissions are incorrect.' );
    }

    private function acf_write_json_field_group( $field_group )
    {
        // vars
        $path = $this->json_location;
        $file = $field_group['key'] . '.json';

        // remove trailing slash
        $path = untrailingslashit( $path );

        // bail early if dir does not exist
        if( !is_writable($path) ) {
            add_action( 'admin_notices', [$this, 'admin_notice_error__acf_folder'] );
            return false;
        }

        // prepare for export
        $id = acf_extract_var( $field_group, 'ID' );
        $field_group = acf_prepare_field_group_for_export( $field_group );

        // If we are processing modules...
        if ( $field_group['key'] === $this->module_key ) {
            // Extract layouts
            $layouts = $field_group['fields'][0]['layouts'];
            // Set layouts to empty array
            $field_group['fields'][0]['layouts'] = [];

            $this->save_module_json($layouts);
        }

        // add modified time
        $field_group['modified'] = get_post_modified_time('U', true, $id, true);

        // write file
        $f = fopen("{$path}/{$file}", 'w');
        fwrite($f, acf_json_encode( $field_group ));
        fclose($f);

        // return
        return true;

    }

    private function save_module_json($layouts)
    {
        $path = $this->module_location;

        $path = untrailingslashit( $path );

        foreach ( $layouts as $layout ) {
            if ( $layout['name'] === '' ) {
                add_action( 'admin_notices', [$this, 'admin_notice_error__layout_name'] );
                continue;
            }

            $namespace = $this->to_pascal_case($layout['name']);

            $file = "{$namespace}/fields.json";

            // Skip this module if folder doesn't exists or folder is not writable
            if( !is_writable("{$path}/{$namespace}/") ) {
                add_action( 'admin_notices', [$this, 'admin_notice_error__module_folder'] );
                continue;
            }

            $f = fopen("{$path}/{$file}", 'w');
            fwrite($f, acf_json_encode( $layout ));
            fclose($f);
        }

        return true;
    }

    private function include_module_fields()
    {
        // vars
        $path = $this->module_location;

        // remove trailing slash
        $path = untrailingslashit( $path );

        // check that path exists
        if( !file_exists( $path ) ) {
            return;
        }

        $dir = opendir( $path );

        $return = [];

        while(false !== ( $folder = readdir($dir)) ) {
            if ( !is_dir("{$path}/{$folder}") || !file_exists("{$path}/{$folder}/fields.json") ) {
                continue;
            }

            // read json
            $json = file_get_contents("{$path}/{$folder}/fields.json");

            // validate json
            if( empty($json) ) {
                continue;
            }

            $return[] = json_decode($json, true);
        }

        return $return;
    }

    private function to_pascal_case($string, $delimeter = '_')
    {
        $parts = explode($delimeter, $string);

        $parts = array_map(function ($word) {
            return ucfirst($word);
        }, $parts);

        return implode('', $parts);
    }
}
