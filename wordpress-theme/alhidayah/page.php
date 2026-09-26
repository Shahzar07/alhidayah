<?php
/**
 * Pages: Elementor pages render full width; content pages use the editorial layout;
 * WooCommerce pages (cart, checkout, account) get their own wrappers.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	if ( alhidayah_is_elementor_page() || ( function_exists( 'is_checkout' ) && is_checkout() ) ) {
		the_content();
		continue;
	}
	$is_wc = function_exists( 'is_cart' ) && ( is_cart() || is_account_page() );
	?>
	<section class="ah-page<?php echo $is_wc ? ' ah-page--wc' : ''; ?>">
		<div class="ah-page-inner">
			<header class="ah-page-head reveal-in">
				<span class="soft-pill"><?php echo esc_html( $is_wc ? alhidayah_opt( 'brand_name' ) : ( wp_get_post_parent_id() ? get_the_title( wp_get_post_parent_id() ) : alhidayah_opt( 'brand_name' ) ) ); ?></span>
				<?php the_title( '<h1>', '</h1>' ); ?>
			</header>
			<div class="entry-content reveal-in">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
		</div>
	</section>
	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="ah-comments">';
		comments_template();
		echo '</div>';
	}
}

get_footer();
