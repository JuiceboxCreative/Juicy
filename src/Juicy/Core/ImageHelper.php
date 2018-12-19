<?php

namespace Juicy\Core;

use Timber\ImageHelper as TimberImageHelper;
use Timber\URLHelper;

class ImageHelper extends TimberImageHelper {

    public static function letterbox( $src, $w, $h, $color = false, $force = false, $use_timber = false) {
        if ($src instanceof Image) {
            $src = $src->src();
        }

        if (empty(env('CLOUDINARY_URL', '')) || $use_timber) {
            // Dont crop if its a gif as the servers don't have 
            // libraries to support this
            $ext = pathinfo($src, PATHINFO_EXTENSION);            
            if($ext == 'gif') {
                return $src;
            }

            // Fix images in month folders.
            if (URLHelper::is_external_content($src)) {
                // Fixes: external images are downloaded every month (#1098)
                add_filter('upload_dir', array(__CLASS__, 'setUploadDir'));

                // Fix sideload issue: uppercase image extensions (#829)
                $fileLoc = self::get_sideloaded_file_loc($src);
                $file = pathinfo($fileLoc);
                $lowercaseExtension = self::getLowercaseExtension($fileLoc, $file['extension']);

                if (file_exists($lowercaseExtension)) {
                    // Return existing file URL
                    $src = URLHelper::get_rel_path($lowercaseExtension);
                }
            }

            $result = parent::letterbox($src, $w, $h, strpos($color, '#')  === 0 ? $color : '#' . $color, $force);

            remove_filter('upload_dir', array(__CLASS__, 'setUploadDir'));

            return $result;
        }

        $base_url = env('CLOUDINARY_URL') . '/image/fetch/';

        $base_filters = 'c_pad';

        $filters = $base_filters . ',b_rgb:' . str_replace('#', '', $color);

        if ($w && is_numeric($w)) {
            $filters .= ',w_' . $w;
        }

        if ($h && is_numeric($h)) {
            $filters .= ',h_' . $h;
        }

        return $base_url . $filters . '/' . $src;
    }

    public static function resize( $src, $w = 0, $h = 0, $filters = 'c_fill,g_auto', $use_timber = false) {
        if ($src instanceof Image) {
            $src = $src->src();
        }

        if (empty(env('CLOUDINARY_URL', '')) || $use_timber) {
            // Dont crop if its a gif as the servers don't have 
            // libraries to support this
            $ext = pathinfo($src, PATHINFO_EXTENSION);            
            if($ext == 'gif') {
                return $src;
            }
            
            // Maintaining backwards compat.
            if ($filters == 'c_fill,g_auto') {
                $filters = 'center';
            }
            $crop = $filters;

            // Fix images in month folders.
            if (URLHelper::is_external_content($src)) {
                // Fixes: external images are downloaded every month (#1098)
                add_filter('upload_dir', array(__CLASS__, 'setUploadDir'));

                // Fix sideload issue: uppercase image extensions (#829)
                $fileLoc = self::get_sideloaded_file_loc($src);
                $file = pathinfo($fileLoc);
                $lowercaseExtension = self::getLowercaseExtension($fileLoc, $file['extension']);

                if (file_exists($lowercaseExtension)) {
                    // Return existing file URL
                    $src = URLHelper::get_rel_path($lowercaseExtension);
                }
            }

            $result =  parent::resize($src, $w, $h, $crop);

            remove_filter('upload_dir', array(__CLASS__, 'setUploadDir'));

            return $result;
        }

        $base_url = env('CLOUDINARY_URL') . '/image/fetch/';

        // Filters to use on every image.
        $base_filters = 'f_auto,dpr_auto';

        if (!empty($filters)) {
            if ($filters == 'center') {
                $filters = 'c_fill,g_auto';
            }

            $filters .= ends_with($filters, '/') ? $base_filters : (',' . $base_filters);
        }
        else {
            $filters = $base_filters;
        }

        if ($w && is_numeric($w)) {
            $filters .= ',w_' . $w;
        }

        if ($h && is_numeric($h)) {
            $filters .= ',h_' . $h;
        }

        return $base_url . $filters . '/' . $src;
    }

    public static function setUploadDir($upload)
    {
        $upload['subdir'] = '/timber';
        $upload['path'] = $upload['basedir'] . $upload['subdir'];
        $upload['url'] = $upload['baseurl'] . $upload['subdir'];

        return $upload;
    }

    private static function getLowercaseExtension($src, $extension)
    {
        return str_replace('.' . $extension, '.' . strtolower($extension), $src);
    }
}
