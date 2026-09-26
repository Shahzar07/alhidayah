<?php
/**
 * Template Name: Full width (for Elementor)
 * Template Post Type: page
 *
 * Header and footer with a full-width, unwrapped content area.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	the_content();
}
get_footer();
