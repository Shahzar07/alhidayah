<?php
/**
 * Checkout form, Shopify-style: contact → delivery → shipping → payment on the left,
 * order summary on the right (collapsible on mobile).
 *
 * Override of woocommerce/templates/checkout/form-checkout.php.
 *
 * @package AlHidayah
 * @version 9.4.0
 *
 * @var WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'alhidayah' ) ) );
	return;
}

$fields  = $checkout->get_checkout_fields( 'billing' );
$contact = array_intersect_key( $fields, array_flip( array( 'billing_email', 'billing_phone' ) ) );
$address = array_diff_key( $fields, $contact );
$cart    = WC()->cart;
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout ah-co" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php esc_attr_e( 'Checkout', 'alhidayah' ); ?>">
	<div class="ah-co-grid">
		<button type="button" class="ah-summary-toggle" aria-expanded="false" aria-controls="ah-co-summary" data-ah-summary-toggle>
			<span><?php alhidayah_icon( 'shopping-bag', 16 ); ?> <span data-show><?php esc_html_e( 'Show order summary', 'alhidayah' ); ?></span><span data-hide hidden><?php esc_html_e( 'Hide order summary', 'alhidayah' ); ?></span> <?php alhidayah_icon( 'chevron-down', 16, array( 'class' => 'ah-chevron' ) ); ?></span>
			<?php wc_get_template( 'checkout/ah-summary-toggle.php' ); ?>
		</button>
		<div class="ah-co-main">

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div id="customer_details">
				<section class="ah-co-section woocommerce-billing-fields">
					<div class="ah-co-head">
						<h2><?php esc_html_e( 'Contact', 'alhidayah' ); ?></h2>
						<?php if ( ! is_user_logged_in() && 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) : ?>
							<a href="<?php echo esc_url( add_query_arg( 'redirect_to', rawurlencode( wc_get_checkout_url() ), wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Sign in', 'alhidayah' ); ?></a>
						<?php endif; ?>
					</div>
					<div class="ah-fields">
						<?php
						foreach ( $contact as $key => $field ) {
							woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						}
						?>
					</div>
				</section>

				<section class="ah-co-section">
					<div class="ah-co-head"><h2><?php esc_html_e( 'Delivery', 'alhidayah' ); ?></h2></div>
					<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
					<div class="ah-fields woocommerce-billing-fields__field-wrapper">
						<?php
						foreach ( $address as $key => $field ) {
							woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						}
						?>
					</div>
					<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>

					<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
						<div class="woocommerce-account-fields ah-fields">
							<?php if ( ! $checkout->is_registration_required() ) : ?>
								<p class="form-row form-row-wide create-account">
									<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
										<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'alhidayah' ); ?></span>
									</label>
								</p>
							<?php endif; ?>
							<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>
							<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
								<div class="create-account">
									<?php
									foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) {
										woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
									}
									?>
								</div>
							<?php endif; ?>
							<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
						</div>
					<?php endif; ?>
				</section>

				<?php if ( $cart->needs_shipping() ) : ?>
					<section class="ah-co-section">
						<div class="ah-co-head"><h2><?php esc_html_e( 'Shipping method', 'alhidayah' ); ?></h2></div>
						<?php AlHidayah_WooCommerce::shipping_methods(); ?>
					</section>
				<?php endif; ?>

				<?php if ( $checkout->get_checkout_fields( 'order' ) ) : ?>
					<section class="ah-co-section ah-co-notes woocommerce-additional-fields">
						<details>
							<summary><?php esc_html_e( 'Add a note to your order', 'alhidayah' ); ?></summary>
							<div class="ah-fields woocommerce-additional-fields__field-wrapper">
								<?php
								foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) {
									woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
								}
								?>
							</div>
						</details>
					</section>
				<?php endif; ?>

				<section class="ah-co-section ah-co-payment">
					<div class="ah-co-head"><h2><?php esc_html_e( 'Payment', 'alhidayah' ); ?></h2></div>
					<p class="ah-co-sub"><?php alhidayah_icon( 'lock', 13 ); ?> <?php esc_html_e( 'All transactions are secure and encrypted.', 'alhidayah' ); ?></p>
					<?php woocommerce_checkout_payment(); ?>
				</section>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<nav class="ah-co-policies" aria-label="<?php esc_attr_e( 'Store policies', 'alhidayah' ); ?>">
				<a href="<?php echo esc_url( alhidayah_page_url( 'refund-policy' ) ); ?>"><?php esc_html_e( 'Refund policy', 'alhidayah' ); ?></a>
				<a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : alhidayah_page_url( 'privacy-policy' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'alhidayah' ); ?></a>
				<a href="<?php echo esc_url( alhidayah_page_url( 'terms-of-service' ) ); ?>"><?php esc_html_e( 'Terms of service', 'alhidayah' ); ?></a>
				<a href="<?php echo esc_url( alhidayah_contact_url() ); ?>"><?php esc_html_e( 'Contact', 'alhidayah' ); ?></a>
			</nav>
		</div>

		<aside class="ah-co-summary" id="ah-co-summary" aria-label="<?php esc_attr_e( 'Order summary', 'alhidayah' ); ?>">
			<div class="ah-co-summary-inner">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
				<h2 class="screen-reader-text" id="order_review_heading"><?php esc_html_e( 'Your order', 'alhidayah' ); ?></h2>
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php woocommerce_order_review(); ?>
				</div>
				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</aside>
	</div>
</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
