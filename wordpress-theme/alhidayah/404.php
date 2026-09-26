<?php
/**
 * Not found.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="ah-page ah-404">
	<div class="ah-page-inner">
		<header class="ah-page-head reveal-in">
			<span class="soft-pill">404</span>
			<h1><?php esc_html_e( 'This page has drifted away', 'alhidayah' ); ?></h1>
		</header>
		<div class="entry-content reveal-in">
			<p><?php esc_html_e( 'The page you are looking for may have moved. Let us guide you back to something beautiful.', 'alhidayah' ); ?></p>
			<p><a class="button black" href="<?php echo esc_url( home_url( '/#shop' ) ); ?>"><?php esc_html_e( 'Explore fragrances', 'alhidayah' ); ?> <?php alhidayah_icon( 'arrow-up-right', 15 ); ?></a></p>
		</div>
	</div>
</section>
<?php
get_footer();
