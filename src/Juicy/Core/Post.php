<?php

namespace Juicy\Core;

use TimberPost;

class Post extends TimberPost
{
    use HasModuleLoop;

    public $PostClass = '\\Juicy\\Core\\Post';
    public $ImageClass = '\\Juicy\\Core\\Image';
    public $modules = null;
    public $subnav = null;

    /**
     * @return PostPreview
     */
    public function preview()
    {
        return new PostPreview($this);
    }

    public function get_subnav()
    {
        if ($this->subnav !== null) {
            return $this->subnav;
        }

        $this->subnav = new Subnav($this);

        return $this->subnav;

    }

    public function get_thumbnail($fallback = false)
    {
        $tid = get_post_thumbnail_id($this->ID);

        if ( $tid ) {
            return new $this->ImageClass((int) $tid);
        }

        if ($fallback) {
            $default = get_field($fallback, 'option');

            if (! empty($default) ) {

                if ($default instanceof $this->ImageClass) {
                    return $default;
                }

                return new $this->ImageClass($default);
            }
        }

        return null;
    }

    public function thumbnail($fallback = false)
    {
        return $this->get_thumbnail($fallback);
    }

    public function get_field($selector)
    {
        return get_field($selector, $this->ID);
    }

    public function get_fb_share_link()
    {
        return 'https://www.facebook.com/sharer/sharer.php?u='.$this->link;
    }

    public function get_twitter_share_link()
    {
        return 'https://twitter.com/share?text='.urlencode(html_entity_decode($this->title . " - ". get_bloginfo('name'), ENT_COMPAT, 'UTF-8'))."&amp;url=" . get_bloginfo('url') . "?p=" . $this->ID;
    }

    public function get_linkedin_share_link()
    {
        return 'https://www.linkedin.com/shareArticle?mini=true&url=' . $this->link .  '&amp;title=' . urlencode(html_entity_decode($this->title . " - ". get_bloginfo('name'), ENT_COMPAT, 'UTF-8'));
    }

    public function has_children() {
        $children = $this->get_children();
        return (is_array($children) && count($children) > 0);
    }

    /**
     * Gets the Yoast primary term for the post,
     * or the first one defined if an error.
     *
     * @return array|bool containing the following:
     *      'id' => the category id
     *      'display' => the category name
     *      'link' => link to the category page
     *      or FALSE if post has no category assigned.
     */
    public function get_primary_term($taxonomy = 'category')
    {
        $category = get_the_terms($this->ID, $taxonomy);
        if (!$category) {
            return false;
        }

        $return = array(
            'id' => $category[0]->term_id,
            'display' => $category[0]->name,
            'link' => get_term_link($category[0]->term_id, $taxonomy),
        );

        if (class_exists('\WPSEO_Primary_Term')) {
            // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
            $wpseo_primary_term = new \WPSEO_Primary_Term($taxonomy, $this->ID);
            $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
            $term = get_term($wpseo_primary_term);
            if (!is_wp_error($term)) {
                // Yoast Primary category
                $category_display = $term->name;
                $return['id'] = $term->term_id;
                $category_link = get_term_link($term->term_id, $taxonomy);
            }
        }

        // Return category data.
        if (!empty($category_display)) {
            if (!empty($category_link)) {
                $return['link'] = $category_link;
            }

            $return['display'] = htmlspecialchars($category_display);
        }

        return $return;
    }

    public function get_primary_category() {
        return $this->get_primary_term('category');
    }
}
