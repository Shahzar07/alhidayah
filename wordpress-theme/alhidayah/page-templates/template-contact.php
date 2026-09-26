<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Contact details, the inquiry form (saved to Forms in the dashboard) and FAQ.
 * Any page content is shown below the form.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	alhidayah_section( 'contact' );
	if ( trim( get_the_content() ) ) {
		echo '<section class="ah-page"><div class="ah-page-inner"><div class="entry-content">';
		the_content();
		echo '</div></div></section>';
	}
}
get_footer();
