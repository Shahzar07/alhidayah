<?php
/**
 * Alhidayah theme bootstrap.
 *
 * All logic lives in /inc. This file only defines constants and loads modules.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

define( 'ALHIDAYAH_VERSION', '1.0.0' );
define( 'ALHIDAYAH_DIR', get_template_directory() );
define( 'ALHIDAYAH_URI', get_template_directory_uri() );

require_once ALHIDAYAH_DIR . '/inc/helpers.php';
require_once ALHIDAYAH_DIR . '/inc/class-alhidayah-setup.php';
require_once ALHIDAYAH_DIR . '/inc/class-alhidayah-assets.php';
require_once ALHIDAYAH_DIR . '/inc/class-alhidayah-customizer.php';
require_once ALHIDAYAH_DIR . '/inc/class-alhidayah-demo-import.php';

AlHidayah_Setup::init();
AlHidayah_Assets::init();
AlHidayah_Customizer::init();
AlHidayah_Demo_Import::init();

if ( class_exists( 'WooCommerce' ) ) {
	require_once ALHIDAYAH_DIR . '/inc/class-alhidayah-woocommerce.php';
	AlHidayah_WooCommerce::init();
}

/*
 * The Forms inbox, testimonials, Elementor widgets, SEO meta and shortcodes live in the bundled
 * "Alhidayah Core" plugin (plugin territory). Appearance → Alhidayah Setup installs it in one click.
 */
