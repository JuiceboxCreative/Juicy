<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package 	WordPress
 * @subpackage 	Timber
 * @since 		Timber 0.1
 */

$context = Timber::get_context();

$context['title'] = 'Search results for '. get_search_query();
$context['posts'] = Timber::get_posts(false, "\\Juicy\\Core\\Post");

Timber::render(array('search.twig', 'archive.twig', 'index.twig'), $context);
