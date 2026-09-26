<?php
/**
 * Theme supports, menus, image sizes and widget areas.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup.
 */
class AlHidayah_Setup {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'after_setup_theme', array( __CLASS__, 'setup' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'theme_page_templates', array( __CLASS__, 'page_templates' ) );
		add_action( 'wp_head', array( __CLASS__, 'favicon' ), 2 );
	}

	/**
	 * Core theme supports.
	 */
	public static function setup() {
		load_theme_textdomain( 'alhidayah', ALHIDAYAH_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 64,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 600,
				'single_image_width'    => 1200,
			)
		);
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor.css' );

		add_image_size( 'alhidayah-scene', 1200, 1200, true );

		register_nav_menus(
			array(
				'primary'         => __( 'Menu drawer', 'alhidayah' ),
				'footer-shop'     => __( 'Footer: Shop', 'alhidayah' ),
				'footer-care'     => __( 'Footer: Customer Care', 'alhidayah' ),
				'footer-discover' => __( 'Footer: Discover', 'alhidayah' ),
				'footer-legal'    => __( 'Footer: Legal', 'alhidayah' ),
				'footer-connect'  => __( 'Footer: Connect', 'alhidayah' ),
			)
		);

		$GLOBALS['content_width'] = 1320;
	}

	/**
	 * Body classes used by the stylesheet.
	 *
	 * @param string[] $classes Classes.
	 * @return string[]
	 */
	public static function body_class( $classes ) {
		$classes[] = alhidayah_page_has_hero() ? 'ah-has-hero' : 'ah-solid-header';
		return $classes;
	}

	/**
	 * Keep the template list tidy (templates live in /page-templates).
	 *
	 * @param array $templates Templates.
	 * @return array
	 */
	public static function page_templates( $templates ) {
		return $templates;
	}

	/**
	 * Default favicon when no Site Icon is set.
	 */
	public static function favicon() {
		if ( has_site_icon() ) {
			return;
		}
		printf( '<link rel="icon" href="%s" type="image/png">' . "\n", esc_url( alhidayah_asset( 'images/favicon.png' ) ) );
		printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( alhidayah_asset( 'images/apple-touch-icon.png' ) ) );
	}
}
