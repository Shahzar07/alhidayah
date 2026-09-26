<?php
/**
 * Order summary (right column): items with thumbnails and quantity badges, discount code, totals.
 *
 * Override of woocommerce/templates/checkout/review-order.php. The root element keeps the
 * "woocommerce-checkout-review-order-table" class so WooCommerce can refresh it over AJAX.
 *
 * @package AlHidayah
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

$cart  = WC()->cart;
$count = $cart->get_cart_contents_count();
?>
<div class="shop_table woocommerce-checkout-review-order-table ah-review">
	<?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>
	<ul class="ah-review-items">
		<?php
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}
			$p = alhidayah_product( $_product );
			?>
			<li class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<span class="ah-thumb">
					<?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'thumb' ) ); ?>
					<span class="ah-thumb-qty"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
				</span>
				<span class="ah-item-name">
					<strong><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></strong>
					<small><?php echo esc_html( sprintf( /* translators: %s size */ __( 'Eau de parfum · %s', 'alhidayah' ), $p ? $p['size'] : '' ) ); ?></small>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
				<span class="ah-item-price"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', $cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>

	<?php if ( wc_coupons_enabled() ) : ?>
		<div class="ah-coupon" data-ah-coupon-box>
			<label class="screen-reader-text" for="ah-coupon-code"><?php esc_html_e( 'Discount code', 'alhidayah' ); ?></label>
			<input type="text" id="ah-coupon-code" class="input-text" placeholder="<?php esc_attr_e( 'Discount code or gift card', 'alhidayah' ); ?>" autocomplete="off" data-ah-coupon-input>
			<button type="button" class="button outline" data-ah-coupon-apply><?php esc_html_e( 'Apply', 'alhidayah' ); ?></button>
			<p class="ah-coupon-message" data-ah-coupon-message role="status" aria-live="polite" hidden></p>
		</div>
	<?php endif; ?>

	<dl class="ah-totals">
		<div class="cart-subtotal">
			<dt>
				<?php
				/* translators: %d item count */
				echo esc_html( sprintf( _n( 'Subtotal · %d item', 'Subtotal · %d items', $count, 'alhidayah' ), $count ) );
				?>
			</dt>
			<dd><?php wc_cart_totals_subtotal_html(); ?></dd>
		</div>

		<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
			<div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<dt><?php esc_html_e( 'Discount', 'alhidayah' ); ?> <span class="ah-code"><?php alhidayah_icon( 'tag', 12 ); ?> <?php echo esc_html( strtoupper( $code ) ); ?> <a href="#" class="woocommerce-remove-coupon" data-coupon="<?php echo esc_attr( $code ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s coupon */ __( 'Remove %s', 'alhidayah' ), $code ) ); ?>">×</a></span></dt>
				<dd>−<?php echo wp_kses_post( wc_price( $cart->get_coupon_discount_amount( $code, $cart->display_cart_ex_tax ) ) ); ?></dd>
			</div>
		<?php endforeach; ?>

		<?php if ( $cart->needs_shipping() && $cart->show_shipping() ) : ?>
			<?php
			$packages = WC()->shipping()->get_packages();
			$chosen   = WC()->session->get( 'chosen_shipping_methods' );
			$label    = __( 'Enter shipping address', 'alhidayah' );
			foreach ( $packages as $i => $package ) {
				if ( isset( $chosen[ $i ], $package['rates'][ $chosen[ $i ] ] ) ) {
					$rate  = $package['rates'][ $chosen[ $i ] ];
					$cost  = (float) $rate->get_cost() + (float) array_sum( $rate->get_taxes() );
					$label = $cost > 0 ? wc_price( $cost ) : __( 'Free', 'alhidayah' );
				}
			}
			?>
			<div class="shipping"><dt><?php esc_html_e( 'Shipping', 'alhidayah' ); ?></dt><dd><?php echo wp_kses_post( $label ); ?></dd></div>
		<?php endif; ?>

		<?php foreach ( $cart->get_fees() as $fee ) : ?>
			<div class="fee"><dt><?php echo esc_html( $fee->name ); ?></dt><dd><?php wc_cart_totals_fee_html( $fee ); ?></dd></div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! $cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( $cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>"><dt><?php echo esc_html( $tax->label ); ?></dt><dd><?php echo wp_kses_post( $tax->formatted_amount ); ?></dd></div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="tax-total"><dt><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></dt><dd><?php wc_cart_totals_taxes_total_html(); ?></dd></div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
		<div class="order-total ah-total"><dt><?php esc_html_e( 'Total', 'alhidayah' ); ?></dt><dd><small><?php echo esc_html( get_woocommerce_currency() ); ?></small> <?php wc_cart_totals_order_total_html(); ?></dd></div>
		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</dl>
</div>
