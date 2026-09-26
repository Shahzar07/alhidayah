<?php
/**
 * Shopping bag contents (WooCommerce cart).
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
	return;
}
$cart  = WC()->cart;
$count = $cart->get_cart_contents_count();
?>
<h2><?php esc_html_e( 'Your shopping bag', 'alhidayah' ); ?> <span>(<?php echo esc_html( $count ); ?>)</span></h2>
<?php if ( $cart->is_empty() ) : ?>
	<div class="empty-bag">
		<?php alhidayah_icon( 'shopping-bag', 40, array( 'stroke_width' => 1 ) ); ?>
		<h3><?php esc_html_e( 'Your next signature awaits.', 'alhidayah' ); ?></h3>
		<p><?php esc_html_e( 'Discover a fragrance that feels like you.', 'alhidayah' ); ?></p>
		<a class="button black" href="<?php echo esc_url( home_url( '/#shop' ) ); ?>" data-ah-close-link><?php esc_html_e( 'Explore fragrances', 'alhidayah' ); ?></a>
	</div>
<?php else : ?>
	<div class="bag-lines">
		<?php foreach ( $cart->get_cart() as $key => $item ) : ?>
			<?php
			$p = alhidayah_product( $item['data'] );
			if ( ! $p ) {
				continue;
			}
			$max = $item['data']->get_max_purchase_quantity();
			?>
			<div class="bag-line">
				<a href="<?php echo esc_url( $p['url'] ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s product */ __( 'View %s', 'alhidayah' ), $p['name'] ) ); ?>"><?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'thumb' ) ); ?></a>
				<div>
					<h3><?php echo esc_html( $p['name'] ); ?></h3>
					<p><?php echo esc_html( sprintf( /* translators: %s size */ __( 'Eau de parfum · %s', 'alhidayah' ), $p['size'] ) ); ?></p>
					<strong><?php echo esc_html( alhidayah_price( $p['price'] ) ); ?></strong>
					<div class="quantity">
						<button type="button" data-ah-qty="<?php echo esc_attr( $key ); ?>" data-value="<?php echo esc_attr( $item['quantity'] - 1 ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s product */ __( 'Decrease %s quantity', 'alhidayah' ), $p['name'] ) ); ?>"><?php alhidayah_icon( 'minus', 13 ); ?></button>
						<span><?php echo esc_html( $item['quantity'] ); ?></span>
						<button type="button" data-ah-qty="<?php echo esc_attr( $key ); ?>" data-value="<?php echo esc_attr( $item['quantity'] + 1 ); ?>"<?php disabled( $max > 0 && $item['quantity'] >= $max ); ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s product */ __( 'Increase %s quantity', 'alhidayah' ), $p['name'] ) ); ?>"><?php alhidayah_icon( 'plus', 13 ); ?></button>
					</div>
				</div>
				<button class="remove" type="button" data-ah-qty="<?php echo esc_attr( $key ); ?>" data-value="0" aria-label="<?php echo esc_attr( sprintf( /* translators: %s product */ __( 'Remove %s', 'alhidayah' ), $p['name'] ) ); ?>"><?php alhidayah_icon( 'trash-2', 16 ); ?></button>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="bag-summary">
		<div><span><?php esc_html_e( 'Subtotal', 'alhidayah' ); ?></span><strong><?php echo esc_html( wp_strip_all_tags( $cart->get_cart_subtotal() ) ); ?></strong></div>
		<p><?php esc_html_e( 'Shipping and taxes calculated at checkout.', 'alhidayah' ); ?></p>
		<a class="button black" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'alhidayah' ); ?> <?php alhidayah_icon( 'arrow-right', 17 ); ?></a>
	</div>
<?php endif; ?>
