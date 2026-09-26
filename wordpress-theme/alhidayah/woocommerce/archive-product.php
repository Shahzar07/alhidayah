<?php
/**
 * Shop and product category archives, using the design's product grid.
 *
 * Override of woocommerce/templates/archive-product.php.
 *
 * @package AlHidayah
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$products = array();
if ( woocommerce_product_loop() ) {
	while ( have_posts() ) {
		the_post();
		$data = alhidayah_product( get_the_ID() );
		if ( $data ) {
			$products[] = $data;
		}
	}
}
$is_term = is_product_taxonomy();
$term    = $is_term ? get_queried_object() : null;
?>
<div class="ah-shop-page">
	<?php do_action( 'woocommerce_before_main_content' ); ?>
	<?php wc_print_notices(); ?>
	<?php
	alhidayah_section(
		'shop',
		array(
			'eyebrow'    => $is_term ? __( 'The collection', 'alhidayah' ) : __( 'Our products', 'alhidayah' ),
			'heading'    => $is_term ? esc_html( $term->name ) : __( 'We know you love lots of<br>scents, discover them now', 'alhidayah' ),
			'show_tools' => ! $is_term && ! is_search(),
			'products'   => $products,
		)
	);
	?>
	<div class="ah-pagination"><?php woocommerce_pagination(); ?></div>
	<?php do_action( 'woocommerce_after_main_content' ); ?>
</div>
<?php
alhidayah_section( 'testimonials' );
get_footer();
