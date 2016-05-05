<?php

namespace Juicy\Core;

use Timber;

class Subnav
{
    public $post;
    public $parent_post = null;
    public $title = null;
    public $items = null;

    public function __construct($post)
    {
        $this->post = (is_int($post)) ? new Post($post) : $post;

        if ($this->post->post_type === 'page' && $post->post_parent) {
            $this->parent_post = new Post($post->post_parent);
        }
    }

    public function getTitle()
    {
        if ($this->title === null) {
            if ($this->post->post_type === 'page') {
                $post = ($this->parent_post === null) ? $this->post : $this->parent_post;
                $this->title = $post->post_title;
            } else {
                $type = $this->post->get_post_type();
                $this->title = $type->labels->name;
            }
        }

        return $this->title;
    }

    public function getItems()
    {
        if ($this->items === null) {
            if ($this->post->post_type == 'page') {
                $post = ($this->parent_post === null) ? $this->post : $this->parent_post;
                $this->items = $post->get_children('parent');
            } else {
                $this->items = $this->getPostTypeSibilings($this->post->post_type);
            }
        }

        return $this->items;
    }

    private function getPostTypeSibilings($post_type)
    {
        $args = array(
            "post_type" => $post_type,
            "posts_per_page" => 5
        );

        $posts = Timber::get_posts($args, $this->post->PostClass);

        if(count($posts) < 2) return array();

        return $posts;
    }
}
