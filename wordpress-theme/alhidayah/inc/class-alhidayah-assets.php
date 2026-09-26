<?php
/**
 * Styles, scripts, preloads.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset loading.
 */
class AlHidayah_Assets {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_action( 'wp_head', array( __CLASS__, 'preload' ), 1 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin' ) );
	}

	/**
	 * Front-end assets.
	 */
	public static function enqueue() {
		$v = ALHIDAYAH_VERSION;

		wp_enqueue_style( 'alhidayah', alhidayah_asset( 'css/theme.css' ), array(), $v );
		if ( alhidayah_has_wc() ) {
			wp_enqueue_style( 'alhidayah-woocommerce', alhidayah_asset( 'css/woocommerce.css' ), array( 'alhidayah' ), $v );
		}

		wp_register_script( 'gsap', alhidayah_asset( 'js/vendor/gsap.min.js' ), array(), '3.15.0', array( 'in_footer' => true ) );
		wp_register_script( 'gsap-scrolltrigger', alhidayah_asset( 'js/vendor/ScrollTrigger.min.js' ), array( 'gsap' ), '3.15.0', array( 'in_footer' => true ) );
		wp_register_script( 'lenis', alhidayah_asset( 'js/vendor/lenis.min.js' ), array(), '1.3.26', array( 'in_footer' => true ) );

		$is_checkout = function_exists( 'is_checkout' ) && ( is_checkout() || is_cart() || is_account_page() );
		$deps        = $is_checkout ? array() : array( 'gsap', 'gsap-scrolltrigger', 'lenis' );
		wp_enqueue_script( 'alhidayah', alhidayah_asset( 'js/theme.js' ), $deps, $v, array( 'in_footer' => true ) );

		wp_localize_script(
			'alhidayah',
			'alhidayahData',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'cartNonce'    => wp_create_nonce( 'alhidayah_cart' ),
				'contactNonce' => wp_create_nonce( 'alhidayah_contact' ),
				'whatsapp'     => alhidayah_whatsapp(),
				'brand'        => alhidayah_opt( 'brand_name' ),
				'checkoutUrl'  => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
				'motion'       => ! $is_checkout,
				'i18n'         => array(
					/* translators: %s: product name */
					'added'        => __( '%s added to your bag', 'alhidayah' ),
					'viewBag'      => __( 'View bag', 'alhidayah' ),
					'error'        => __( 'Something went wrong. Please try again.', 'alhidayah' ),
					'name'         => __( 'Please enter your name.', 'alhidayah' ),
					'email'        => __( 'Please enter a valid email address.', 'alhidayah' ),
					'phone'        => __( 'Please enter a valid phone number.', 'alhidayah' ),
					'message'      => __( 'Please write a little more (at least 10 characters).', 'alhidayah' ),
					'sending'      => __( 'Sending…', 'alhidayah' ),
					'send'         => __( 'Send message', 'alhidayah' ),
					'cleared'      => __( 'Your saved shopping bag has been cleared', 'alhidayah' ),
				),
			)
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Preload the fonts and, on the home page, the hero photo (LCP).
	 */
	public static function preload() {
		foreach ( array( 'regular', 'medium' ) as $weight ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( alhidayah_asset( 'fonts/manrope-' . $weight . '.woff2' ) ) );
		}
		if ( alhidayah_page_has_hero() && is_front_page() ) {
			$desktop = alhidayah_opt( 'hero_image' ) ? alhidayah_opt( 'hero_image' ) : alhidayah_asset( 'images/hero.jpg' );
			$mobile  = alhidayah_opt( 'hero_image_mobile' ) ? alhidayah_opt( 'hero_image_mobile' ) : alhidayah_asset( 'images/hero-mobile.jpg' );
			printf( '<link rel="preload" as="image" href="%s" media="(min-width: 641px)" fetchpriority="high">' . "\n", esc_url( $desktop ) );
			printf( '<link rel="preload" as="image" href="%s" media="(max-width: 640px)" fetchpriority="high">' . "\n", esc_url( $mobile ) );
		}
	}

	/**
	 * Admin styles for the Forms inbox, product fields and setup screen.
	 */
	public static function admin() {
		wp_enqueue_style( 'alhidayah-admin', alhidayah_asset( 'css/admin.css' ), array(), ALHIDAYAH_VERSION );
	}
}
