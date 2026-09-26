<?php
/**
 * Plugin Name:       Alhidayah Core
 * Description:       Companion plugin for the Alhidayah theme: the Forms inbox for contact inquiries, customer testimonials, Elementor widgets, SEO meta and the [alhidayah_contact_links] shortcode.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Alhidayah
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       alhidayah-core
 * Domain Path:       /languages
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'ALHIDAYAH_CORE_VERSION', '1.0.0' );
define( 'ALHIDAYAH_CORE_FILE', __FILE__ );
define( 'ALHIDAYAH_CORE_DIR', __DIR__ );
define( 'ALHIDAYAH_CORE_URI', plugin_dir_url( __FILE__ ) );

require_once ALHIDAYAH_CORE_DIR . '/includes/class-alhidayah-forms.php';
require_once ALHIDAYAH_CORE_DIR . '/includes/class-alhidayah-testimonials.php';

// Inquiries and testimonials are content: they stay available in the dashboard whatever theme is active.
AlHidayah_Forms::init();
AlHidayah_Testimonials::init();

add_action(
	'init',
	static function () {
		load_plugin_textdomain( 'alhidayah-core', false, dirname( plugin_basename( ALHIDAYAH_CORE_FILE ) ) . '/languages' );
	}
);

add_action(
	'admin_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'alhidayah-core-admin', ALHIDAYAH_CORE_URI . 'assets/admin.css', array(), ALHIDAYAH_CORE_VERSION );
	}
);

/**
 * Features that render the theme's own templates load only while the Alhidayah theme (or a child theme) is active.
 */
add_action(
	'after_setup_theme',
	static function () {
		if ( ! function_exists( 'alhidayah_section' ) ) {
			return;
		}
		require_once ALHIDAYAH_CORE_DIR . '/includes/class-alhidayah-seo.php';
		require_once ALHIDAYAH_CORE_DIR . '/includes/class-alhidayah-shortcodes.php';
		AlHidayah_SEO::init();
		AlHidayah_Shortcodes::init();

		if ( did_action( 'elementor/loaded' ) ) {
			require_once ALHIDAYAH_CORE_DIR . '/includes/class-alhidayah-elementor.php';
			AlHidayah_Elementor::init();
		}
	},
	20
);

register_activation_hook(
	__FILE__,
	static function () {
		AlHidayah_Forms::register();
		AlHidayah_Testimonials::register();
		flush_rewrite_rules();
	}
);
