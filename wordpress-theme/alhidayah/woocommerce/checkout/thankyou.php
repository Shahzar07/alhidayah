<?php
/**
 * Order confirmation, Shopify-style.
 *
 * Override of woocommerce/templates/checkout/thankyou.php.
 *
 * @package AlHidayah
 * @version 8.1.0
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-order ah-co">
	<?php if ( ! $order ) : ?>
		<div class="ah-co-grid"><div class="ah-co-main">
			<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>
		</div></div>
	<?php elseif ( $order->has_status( 'failed' ) ) : ?>
		<div class="ah-co-grid"><div class="ah-co-main">
			<div class="ah-co-section">
				<h1 class="ah-ty-title"><?php esc_html_e( 'Payment was not completed', 'alhidayah' ); ?></h1>
				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'alhidayah' ); ?></p>
				<p class="ah-ty-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button black pay"><?php esc_html_e( 'Pay', 'alhidayah' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button outline"><?php esc_html_e( 'My account', 'alhidayah' ); ?></a>
					<?php endif; ?>
				</p>
			</div>
		</div></div>
	<?php else : ?>
		<?php
		$name     = $order->get_billing_first_name();
		$shipping = $order->get_formatted_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address();
		?>
		<div class="ah-co-grid">
			<div class="ah-co-main">
				<div class="ah-ty-head">
					<span class="done-icon"><?php alhidayah_icon( 'check', 22 ); ?></span>
					<div>
						<span class="ah-ty-order">
							<?php
							/* translators: %s order number */
							echo esc_html( sprintf( __( 'Order #%s', 'alhidayah' ), $order->get_order_number() ) );
							?>
						</span>
						<h1 class="ah-ty-title">
							<?php
							/* translators: %s first name */
							echo esc_html( $name ? sprintf( __( 'Thank you, %s!', 'alhidayah' ), $name ) : __( 'Thank you!', 'alhidayah' ) );
							?>
						</h1>
					</div>
				</div>

				<div class="ah-co-box">
					<h2><?php esc_html_e( 'Your order is confirmed', 'alhidayah' ); ?></h2>
					<p><?php esc_html_e( 'You’ll receive a confirmation email with your order number shortly. Our team will be in touch before delivery.', 'alhidayah' ); ?></p>
				</div>

				<?php
				// Payment instructions from the gateway (bank details, cash on delivery note…).
				ob_start();
				do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
				$alhidayah_instructions = trim( ob_get_clean() );
				if ( $alhidayah_instructions ) {
					echo '<div class="ah-co-box ah-ty-note">' . $alhidayah_instructions . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Gateway output, escaped by WooCommerce.
				}
				?>

				<div class="ah-co-box ah-ty-details">
					<h2><?php esc_html_e( 'Order details', 'alhidayah' ); ?></h2>
					<dl>
						<div><dt><?php esc_html_e( 'Contact information', 'alhidayah' ); ?></dt><dd><?php echo esc_html( $order->get_billing_email() ); ?><br><?php echo esc_html( $order->get_billing_phone() ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Payment method', 'alhidayah' ); ?></dt><dd><?php echo esc_html( $order->get_payment_method_title() ); ?> · <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Shipping address', 'alhidayah' ); ?></dt><dd><?php echo wp_kses_post( $shipping ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Shipping method', 'alhidayah' ); ?></dt><dd><?php echo esc_html( $order->get_shipping_method() ? $order->get_shipping_method() : __( 'Free', 'alhidayah' ) ); ?></dd></div>
					</dl>
				</div>

				<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

				<div class="ah-ty-actions">
					<p><?php esc_html_e( 'Need help?', 'alhidayah' ); ?> <a href="<?php echo esc_url( alhidayah_contact_url() ); ?>"><?php esc_html_e( 'Contact us', 'alhidayah' ); ?></a></p>
					<a class="button black" href="<?php echo esc_url( home_url( '/#shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'alhidayah' ); ?></a>
				</div>
			</div>

			<aside class="ah-co-summary" aria-label="<?php esc_attr_e( 'Order summary', 'alhidayah' ); ?>">
				<div class="ah-co-summary-inner">
					<div class="ah-review">
						<ul class="ah-review-items">
							<?php
							foreach ( $order->get_items() as $item ) :
								$p = alhidayah_product( $item->get_product() );
								?>
								<li>
									<span class="ah-thumb">
										<?php if ( $p ) { get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'thumb' ) ); } ?>
										<span class="ah-thumb-qty"><?php echo esc_html( $item->get_quantity() ); ?></span>
									</span>
									<span class="ah-item-name"><strong><?php echo esc_html( $item->get_name() ); ?></strong><small><?php echo esc_html( sprintf( /* translators: %s size */ __( 'Eau de parfum · %s', 'alhidayah' ), $p ? $p['size'] : '' ) ); ?></small></span>
									<span class="ah-item-price"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
						<dl class="ah-totals">
							<?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
								<?php
								if ( 'payment_method' === $key ) {
									continue;
								}
								?>
								<div class="<?php echo 'order_total' === $key ? 'ah-total' : 'ah-row-' . esc_attr( $key ); ?>"><dt><?php echo esc_html( rtrim( $total['label'], ':' ) ); ?></dt><dd><?php echo wp_kses_post( $total['value'] ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					</div>
				</div>
			</aside>
		</div>
	<?php endif; ?>
</div>
