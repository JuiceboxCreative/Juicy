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
        if ( $alt !== '' ) {
            return $alt;
        }

        // Fall back to the title.
        return $this->title();
    }

    public function title()
    {
        $title = parent::title();

        // Check if title is set
        if ( $title !== '' ) {
            return $title;
        }

        // Fall back to page title if it is set.
        $title = $this->getPostMeta( 'title' );

        // Otherwise we will use the site name and tagline(if set)
        if ( empty($title) ) {
            $title = $this->getSiteName();
        }

        return $title;
    }

    private function getPostMeta( $type = 'title' )
    {
        global $post;
        return get_post_meta( $post->ID, "_yoast_wpseo_{$type}", true );
    }

    private function getSiteName()
    {
        $ret = get_bloginfo('name');
        $extra = get_bloginfo('description');

        if ( $extra !== '' ) {
            $ret .= " - {$extra}";
        }

        return $ret;
    }
}
