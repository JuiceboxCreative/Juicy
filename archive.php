<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package     WordPress
 * @subpackage  Timber
 * @since       Timber 0.2
 */

$context = Timber::get_context();

$context['post'] = new Juicy\Core\Term();

$context['posts'] = Timber::get_posts(false, "\\Juicy\\Core\\Post");

$context['pagination'] = Timber::get_pagination();

Timber::render(array('index.twig'), $context);
