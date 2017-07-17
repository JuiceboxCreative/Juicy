<?php

namespace JuiceBox\Core;

use Timber\ImageHelper as TimberImageHelper;

class ImageHelper extends TimberImageHelper {

    public static function resize( $src, $w = 0, $h = 0, $filters = 'c_fill,g_auto') {

        if (!empty(env('CLOUDINARY_URL', ''))) {
            // Maintaining backwards compat.
            if ($filters = 'c_fill,g_auto') {
                $filters = 'center';
            }
            $crop = $filters;
            return parent::resize($src, $w, $h, $crop);
        }

        $base_url = env('CLOUDINARY_URL') . '/image/fetch/';

        // Filters to use on every image.
        $base_filters = 'f_auto,dpr_auto';

        if (!empty($filters)) {
            $filters .= ',' . $base_filters;
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
}
