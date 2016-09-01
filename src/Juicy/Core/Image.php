<?php

namespace Juicy\Core;

use TimberImage;

class Image extends TimberImage {
    /**
     * @var int
     */
    private $iid;

    /**
     * @param int $iid
     */
    public function __construct($iid)
    {
        $this->iid = $iid;

        parent::__construct($iid);
    }

    /**
     * @param string|array $size - See http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
     * @return array|bool|string
     */
    public function src($size = 'full')
    {
        $src = wp_get_attachment_image_src($this->iid, $size);
        return $src[0];
    }

    /**
     * @deprecated
     * @param string|array $size - See http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
     * @return array|bool|string
     */
    public function get_src($size = 'full')
    {
        return $this->src($size);
    }

    public function alt()
    {
        $alt = parent::alt();
        // Check if alt is set
        if ( $alt != '' ) {
            return $alt;
        }
        // Otherwise we check for image title
        $alt = parent::title;

        if ( $alt != '' ) {
            return $alt;
        }
        // Lastly fall back to post title
        global $post;
        // post_title because global $post will be WP_Post
        return $post->post_title;
    }
}
