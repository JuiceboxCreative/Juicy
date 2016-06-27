<?php
/**
 * The template for displaying Author Archive pages
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
global $wp_query;

$context = Timber::get_context();
$context['posts'] = Timber::get_posts(false, "\\Juicy\\Core\\Post");

if (isset($wp_query->query_vars['author'])) {
	$author = new TimberUser($wp_query->query_vars['author']);
	$context['author'] = $author;
	$context['title'] = 'Author Archives: ' . $author->name();
}

Timber::render(array('author.twig', 'archive.twig'), $context);
