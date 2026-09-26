<?php
/**
 * Front page. Built with Elementor when the page is edited with it; otherwise the
 * design's default sections are rendered from the Customizer settings.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( alhidayah_is_elementor_page() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	alhidayah_section( 'hero' );
	alhidayah_section( 'collections' );
	alhidayah_section( 'ingredients' );
	alhidayah_section( 'shop' );
	alhidayah_section( 'testimonials' );
	alhidayah_section( 'closing' );
}

get_footer();
