<?php

namespace Juicy\Core;

class SchemaOrgBreadcrumbs
{
    private $breadcrumb_link_counter = 0;
    private $breadcrumb_element_wrapper = 'span';
    private $breadcrumb_output_wrapper = 'span';

    public function __construct()
    {
        add_filter('wpseo_breadcrumb_single_link_wrapper', array($this, 'breadcrumb_element_wrapper'), 95);
        add_filter('wpseo_breadcrumb_output_wrapper', array($this, 'breadcrumb_output_wrapper'), 95);
        add_filter('wpseo_breadcrumb_single_link', array($this, 'modify_breadcrumb_element'), 10, 2);
        add_filter('wpseo_breadcrumb_output', array($this, 'modify_breadcrumb_output'));
    }

    public function breadcrumb_element_wrapper($element)
    {
        $this->breadcrumb_element_wrapper = $element;

        return $element;
    }

    public function breadcrumb_output_wrapper($wrapper)
    {
        $this->breadcrumb_output_wrapper = $wrapper;

        return $wrapper;
    }

    public function modify_breadcrumb_element($link_output, $link)
    {
        $output = '<'.$this->breadcrumb_element_wrapper.' itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';

        if (isset($link['url']) && substr_count($link_output, 'rel="v:url"') > 0) {
            $output .= '<a href="' . esc_attr( $link['url'] ) . '" itemprop="url"><span itemprop="title">' . $link['text'] . '</span></a>';
        } else {
            $opt = get_wpseo_options();
            $tag = isset($opt['breadcrumbs-boldlast']) && $opt['breadcrumbs-boldlast'] ? 'strong' : 'span';
            $output .= '<'.$tag.' class="breadcrumb_last" itemprop="title">' . $link['text'] . '</'.$tag.'>';
        }

        $output .= '</'.$this->breadcrumb_element_wrapper.'>';

        return $output;
    }

    public function modify_breadcrumb_output($full_output)
    {
        return str_replace('prefix="v: http://rdf.data-vocabulary.org/#"', '', $full_output);
    }
}
