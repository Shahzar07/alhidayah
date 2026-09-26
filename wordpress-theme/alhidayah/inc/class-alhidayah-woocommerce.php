<?php
/**
 * WooCommerce integration: product fields, AJAX bag, checkout layout, styling.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce.
 */
class AlHidayah_WooCommerce {

	/**
	 * Product fields shown in the "Al-Hidayah" product data tab.
	 *
	 * @return array[]
	 */
	public static function fields() {
		return array(
			'mood'         => array( __( 'Mood (card subtitle)', 'alhidayah' ), 'text', __( 'Regal, smoky, enveloping', 'alhidayah' ) ),
			'accords'      => array( __( 'Key accords (under the name)', 'alhidayah' ), 'text', __( 'Agarwood, Saffron, Taif Rose', 'alhidayah' ) ),
			'notes_top'    => array( __( 'Top notes (comma separated)', 'alhidayah' ), 'text', __( 'Saffron, Bergamot', 'alhidayah' ) ),
			'notes_middle' => array( __( 'Middle notes (comma separated)', 'alhidayah' ), 'text', __( 'Taif rose, Incense', 'alhidayah' ) ),
			'notes_base'   => array( __( 'Base notes (comma separated)', 'alhidayah' ), 'text', __( 'Oud, Amber, Musk', 'alhidayah' ) ),
			'size'         => array( __( 'Bottle size', 'alhidayah' ), 'text', __( '50 ml', 'alhidayah' ) ),
			'story'        => array( __( 'Story (product page)', 'alhidayah' ), 'textarea', '' ),
			'tint'         => array( __( 'Backdrop tint behind the bottle', 'alhidayah' ), 'color', '#ece6de' ),
			'is_new'       => array( __( 'Show “New” badge (and list in New arrivals)', 'alhidayah' ), 'checkbox', '' ),
			'best_seller'  => array( __( 'List in Best sellers', 'alhidayah' ), 'checkbox', '' ),
			'limited'      => array( __( 'Show “Limited” badge (and list in Limited edition)', 'alhidayah' ), 'checkbox', '' ),
		);
	}

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ), 20 );
		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_panel' ) );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'save_product' ) );

		foreach ( array( 'alhidayah_cart_add', 'alhidayah_cart_update' ) as $action ) {
			add_action( 'wp_ajax_' . $action, array( __CLASS__, $action ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( __CLASS__, $action ) );
		}
		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'fragments' ) );

		add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'checkout_fields' ) );
		add_filter( 'woocommerce_update_order_review_fragments', array( __CLASS__, 'checkout_fragments' ) );
		add_filter( 'woocommerce_order_button_html', array( __CLASS__, 'order_button' ) );
		add_filter( 'woocommerce_checkout_show_terms', '__return_false' );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'loop_shop_per_page', array( __CLASS__, 'per_page' ) );
		add_filter( 'woocommerce_show_page_title', '__return_false' );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'button_text' ) );
		add_action( 'wp', array( __CLASS__, 'adjust_hooks' ) );
	}

	/**
	 * Remove WooCommerce template hooks the design replaces (registered after the theme loads).
	 */
	public static function adjust_hooks() {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
	}

	/**
	 * Checkout script (coupon field, mobile summary toggle).
	 */
	public static function scripts() {
		if ( is_checkout() && ! is_order_received_page() ) {
			wp_enqueue_script( 'alhidayah-checkout', alhidayah_asset( 'js/checkout.js' ), array( 'jquery', 'wc-checkout' ), ALHIDAYAH_VERSION, array( 'in_footer' => true ) );
		}
		if ( is_checkout() || is_account_page() || is_cart() ) {
			// Native selects, like a hosted checkout.
			wp_dequeue_script( 'selectWoo' );
			wp_dequeue_style( 'select2' );
		}
		wp_add_inline_script(
			'alhidayah',
			'window.alhidayahData = window.alhidayahData || {}; window.alhidayahData.currency = ' . wp_json_encode(
				array(
					'symbol'   => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
					'decimals' => wc_get_price_decimals(),
					'thousand' => wc_get_price_thousand_separator(),
					'decimal'  => wc_get_price_decimal_separator(),
					'format'   => html_entity_decode( get_woocommerce_price_format(), ENT_QUOTES, 'UTF-8' ),
				)
			) . ';',
			'before'
		);
	}

	/* ------------------------------------------------------------ product fields */

	/**
	 * Add the product data tab.
	 *
	 * @param array $tabs Tabs.
	 * @return array
	 */
	public static function product_tab( $tabs ) {
		$tabs['alhidayah'] = array(
			'label'    => __( 'Al-Hidayah', 'alhidayah' ),
			'target'   => 'alhidayah_product_data',
			'priority' => 15,
		);
		return $tabs;
	}

	/**
	 * Render the product data panel.
	 */
	public static function product_panel() {
		global $post;
		echo '<div id="alhidayah_product_data" class="panel woocommerce_options_panel hidden"><div class="options_group">';
		echo '<p class="form-field"><em>' . esc_html__( 'These details drive the product card, product page and filters. The featured image is the bottle cut-out; the first gallery image is the lifestyle photo.', 'alhidayah' ) . '</em></p>';
		foreach ( self::fields() as $key => $field ) {
			list( $label, $type, $placeholder ) = $field;
			$id    = '_alhidayah_' . $key;
			$value = get_post_meta( $post->ID, $id, true );
			if ( 'checkbox' === $type ) {
				woocommerce_wp_checkbox(
					array(
						'id'    => $id,
						'label' => $label,
						'value' => $value ? $value : 'no',
					)
				);
			} elseif ( 'textarea' === $type ) {
				woocommerce_wp_textarea_input(
					array(
						'id'    => $id,
						'label' => $label,
						'value' => $value,
					)
				);
			} else {
				woocommerce_wp_text_input(
					array(
						'id'          => $id,
						'label'       => $label,
						'value'       => $value,
						'placeholder' => $placeholder,
						'type'        => 'color' === $type ? 'color' : 'text',
					)
				);
			}
		}
		echo '</div></div>';
	}

	/**
	 * Save product fields.
	 *
	 * @param WC_Product $product Product.
	 */
	public static function save_product( $product ) {
		// Nonce and capability are verified by WooCommerce before this hook runs.
		foreach ( self::fields() as $key => $field ) {
			$id = '_alhidayah_' . $key;
			if ( 'checkbox' === $field[1] ) {
				$product->update_meta_data( $id, isset( $_POST[ $id ] ) ? 'yes' : 'no' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			} elseif ( isset( $_POST[ $id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$raw   = wp_unslash( $_POST[ $id ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$value = 'textarea' === $field[1] ? sanitize_textarea_field( $raw ) : ( 'color' === $field[1] ? sanitize_hex_color( $raw ) : sanitize_text_field( $raw ) );
				$product->update_meta_data( $id, $value );
			}
		}
	}

	/* ------------------------------------------------------------ AJAX bag */

	/**
	 * JSON response with the refreshed bag.
	 *
	 * @param array $extra Extra fields.
	 */
	protected static function bag_response( $extra = array() ) {
		ob_start();
		get_template_part( 'template-parts/components/bag-body' );
		wp_send_json_success(
			array_merge(
				array(
					'count' => WC()->cart->get_cart_contents_count(),
					'html'  => ob_get_clean(),
				),
				$extra
			)
		);
	}

	/**
	 * Add a product to the bag.
	 */
	public static function alhidayah_cart_add() {
		check_ajax_referer( 'alhidayah_cart', 'nonce' );
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$quantity   = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
		$product    = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_purchasable() ) {
			wp_send_json_error( array( 'message' => __( 'This fragrance is not available right now.', 'alhidayah' ) ) );
		}
		wc_clear_notices();
		$added = WC()->cart->add_to_cart( $product_id, $quantity );
		if ( ! $added ) {
			$errors = wc_get_notices( 'error' );
			wc_clear_notices();
			wp_send_json_error( array( 'message' => $errors ? wp_strip_all_tags( $errors[0]['notice'] ) : __( 'Could not add to your bag.', 'alhidayah' ) ) );
		}
		wc_clear_notices();
		self::bag_response( array( 'name' => $product->get_name() ) );
	}

	/**
	 * Change a bag line's quantity (0 removes it).
	 */
	public static function alhidayah_cart_update() {
		check_ajax_referer( 'alhidayah_cart', 'nonce' );
		$key      = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;
		if ( $key && WC()->cart->get_cart_item( $key ) ) {
			if ( $quantity ) {
				WC()->cart->set_quantity( $key, $quantity, true );
			} else {
				WC()->cart->remove_cart_item( $key );
			}
		}
		wc_clear_notices();
		self::bag_response();
	}

	/**
	 * Keep the bag badge in sync when other WooCommerce blocks add to cart.
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public static function fragments( $fragments ) {
		$count = WC()->cart->get_cart_contents_count();
		$fragments['span.bag-count'] = sprintf( '<span class="bag-count" data-ah-count%s>%d</span>', $count ? '' : ' hidden', $count );
		return $fragments;
	}

	/* ------------------------------------------------------------ checkout */

	/**
	 * Shopify-style field set: contact first, then delivery address.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public static function checkout_fields( $fields ) {
		unset( $fields['billing']['billing_company'] );
		$order = array(
			'billing_email'      => array( 5, __( 'Email', 'alhidayah' ), 'form-row-wide' ),
			'billing_phone'      => array( 6, __( 'Phone', 'alhidayah' ), 'form-row-wide' ),
			'billing_country'    => array( 10, __( 'Country / Region', 'alhidayah' ), 'form-row-wide' ),
			'billing_first_name' => array( 20, __( 'First name', 'alhidayah' ), 'form-row-first' ),
			'billing_last_name'  => array( 30, __( 'Last name', 'alhidayah' ), 'form-row-last' ),
			'billing_address_1'  => array( 40, __( 'Address', 'alhidayah' ), 'form-row-wide' ),
			'billing_address_2'  => array( 50, __( 'Apartment, suite, etc. (optional)', 'alhidayah' ), 'form-row-wide' ),
			'billing_city'       => array( 60, __( 'City', 'alhidayah' ), 'form-row-first' ),
			'billing_state'      => array( 70, __( 'State / Province', 'alhidayah' ), 'form-row-last' ),
			'billing_postcode'   => array( 80, __( 'Postal code', 'alhidayah' ), 'form-row-wide' ),
		);
		foreach ( $order as $key => $conf ) {
			if ( ! isset( $fields['billing'][ $key ] ) ) {
				continue;
			}
			$fields['billing'][ $key ]['priority']    = $conf[0];
			$fields['billing'][ $key ]['placeholder'] = $conf[1];
			$classes                                   = array_diff( (array) ( $fields['billing'][ $key ]['class'] ?? array() ), array( 'form-row-first', 'form-row-last', 'form-row-wide' ) );
			$classes[]                                 = $conf[2];
			$fields['billing'][ $key ]['class']        = $classes;
		}
		if ( isset( $fields['order']['order_comments'] ) ) {
			$fields['order']['order_comments']['placeholder'] = __( 'Notes about your order, e.g. a gift message or delivery instructions.', 'alhidayah' );
		}
		return $fields;
	}

	/**
	 * Render the shipping method choices (left column).
	 */
	public static function shipping_methods() {
		echo '<div class="ah-shipping-methods">';
		if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
			$packages = WC()->shipping()->get_packages();
			$chosen   = WC()->session->get( 'chosen_shipping_methods' );
			$any      = false;
			foreach ( $packages as $i => $package ) {
				$rates = $package['rates'];
				if ( ! $rates ) {
					continue;
				}
				$any = true;
				echo '<ul class="ah-options" id="shipping_method">';
				foreach ( $rates as $rate ) {
					$checked = isset( $chosen[ $i ] ) ? $chosen[ $i ] === $rate->id : false;
					if ( ! isset( $chosen[ $i ] ) && reset( $rates ) === $rate ) {
						$checked = true;
					}
					printf(
						'<li><label class="ah-option%5$s"><input type="radio" name="shipping_method[%1$d]" data-index="%1$d" value="%2$s" class="shipping_method"%3$s><span class="ah-option-label">%4$s</span><span class="ah-option-price">%6$s</span></label></li>',
						(int) $i,
						esc_attr( $rate->id ),
						checked( $checked, true, false ),
						esc_html( $rate->get_label() ),
						$checked ? ' is-checked' : '',
						wp_kses_post( (float) $rate->get_cost() > 0 ? wc_price( (float) $rate->get_cost() + (float) array_sum( $rate->get_taxes() ) ) : __( 'Free', 'alhidayah' ) )
					);
				}
				echo '</ul>';
			}
			if ( ! $any ) {
				echo '<p class="ah-muted-box">' . esc_html__( 'Enter your delivery address to see shipping options.', 'alhidayah' ) . '</p>';
			}
		} else {
			echo '<p class="ah-muted-box">' . esc_html__( 'No shipping required.', 'alhidayah' ) . '</p>';
		}
		echo '</div>';
	}

	/**
	 * Refresh the left-column shipping options together with WooCommerce's fragments.
	 *
	 * @param array $fragments Fragments.
	 * @return array
	 */
	public static function checkout_fragments( $fragments ) {
		ob_start();
		self::shipping_methods();
		$fragments['.ah-shipping-methods'] = ob_get_clean();
		ob_start();
		wc_get_template( 'checkout/ah-summary-toggle.php' );
		$fragments['.ah-summary-total'] = ob_get_clean();
		return $fragments;
	}

	/**
	 * Full-width "Pay now" button.
	 *
	 * @param string $html Button HTML.
	 * @return string
	 */
	public static function order_button( $html ) {
		return str_replace( 'class="button alt', 'class="button black ah-pay button alt', $html );
	}

	/**
	 * Body classes for WooCommerce layouts.
	 *
	 * @param array $classes Classes.
	 * @return array
	 */
	public static function body_class( $classes ) {
		if ( is_checkout() && ! is_order_received_page() ) {
			$classes[] = 'ah-checkout';
		}
		if ( is_order_received_page() ) {
			$classes[] = 'ah-checkout ah-thankyou';
		}
		return $classes;
	}

	/**
	 * Products per shop page.
	 *
	 * @return int
	 */
	public static function per_page() {
		return 24;
	}

	/**
	 * Add to cart text.
	 *
	 * @return string
	 */
	public static function button_text() {
		return __( 'I want this', 'alhidayah' );
	}

	/**
	 * Catalogue-ordered product IDs (for Prev/Next on product pages).
	 *
	 * @return int[]
	 */
	public static function catalogue_ids() {
		return wc_get_products(
			array(
				'status'     => 'publish',
				'limit'      => -1,
				'return'     => 'ids',
				'visibility' => 'catalog',
				'orderby'    => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		);
	}
}
