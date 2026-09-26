<?php
/**
 * Elementor integration: "Al-Hidayah" widget category, section widgets, theme locations.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Elementor.
 */
class AlHidayah_Elementor {

	/**
	 * Widget classes keyed by file name.
	 *
	 * @return string[]
	 */
	public static function widgets() {
		return array(
			'hero'         => 'AlHidayah_Widget_Hero',
			'collections'  => 'AlHidayah_Widget_Collections',
			'ingredients'  => 'AlHidayah_Widget_Ingredients',
			'products'     => 'AlHidayah_Widget_Products',
			'testimonials' => 'AlHidayah_Widget_Testimonials',
			'closing'      => 'AlHidayah_Widget_Closing',
			'contact'      => 'AlHidayah_Widget_Contact',
		);
	}

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register' ) );
		add_action( 'elementor/theme/register_locations', array( __CLASS__, 'locations' ) );
		add_filter( 'elementor/fonts/additional_fonts', array( __CLASS__, 'fonts' ) );
	}

	/**
	 * Manrope ships with the theme, so Elementor must not fetch it from Google Fonts.
	 *
	 * @param array $fonts Additional fonts.
	 * @return array
	 */
	public static function fonts( $fonts ) {
		$fonts['Manrope'] = 'system';
		return $fonts;
	}

	/**
	 * Point the active Elementor kit's global colours and fonts at the theme's palette and Manrope.
	 */
	public static function setup_kit() {
		$kit = (int) get_option( 'elementor_active_kit' );
		if ( ! $kit ) {
			return;
		}
		$settings = (array) get_post_meta( $kit, '_elementor_page_settings', true );
		$colors   = array(
			'primary'   => array( __( 'Ink', 'alhidayah-core' ), '#151513' ),
			'secondary' => array( __( 'Bronze', 'alhidayah-core' ), '#a66926' ),
			'text'      => array( __( 'Text', 'alhidayah-core' ), '#151513' ),
			'accent'    => array( __( 'Amber', 'alhidayah-core' ), '#b17137' ),
		);
		$settings['system_colors'] = array();
		foreach ( $colors as $id => $color ) {
			$settings['system_colors'][] = array(
				'_id'   => $id,
				'title' => $color[0],
				'color' => $color[1],
			);
		}
		$fonts                         = array(
			'primary'   => array( __( 'Headings', 'alhidayah-core' ), '500' ),
			'secondary' => array( __( 'Subtitles', 'alhidayah-core' ), '500' ),
			'text'      => array( __( 'Body', 'alhidayah-core' ), '400' ),
			'accent'    => array( __( 'Buttons', 'alhidayah-core' ), '500' ),
		);
		$settings['system_typography'] = array();
		foreach ( $fonts as $id => $font ) {
			$settings['system_typography'][] = array(
				'_id'                    => $id,
				'title'                  => $font[0],
				'typography_typography'  => 'custom',
				'typography_font_family' => 'Manrope',
				'typography_font_weight' => $font[1],
			);
		}
		$settings['default_generic_fonts'] = 'Manrope, Arial, sans-serif';
		update_post_meta( $kit, '_elementor_page_settings', $settings );
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	/**
	 * Widget category.
	 *
	 * @param \Elementor\Elements_Manager $manager Manager.
	 */
	public static function category( $manager ) {
		$manager->add_category(
			'alhidayah',
			array(
				'title' => __( 'Al-Hidayah', 'alhidayah-core' ),
				'icon'  => 'eicon-star',
			)
		);
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $manager Manager.
	 */
	public static function register( $manager ) {
		require_once ALHIDAYAH_CORE_DIR . '/elementor/widgets/class-alhidayah-widget-base.php';
		foreach ( self::widgets() as $file => $class ) {
			require_once ALHIDAYAH_CORE_DIR . '/elementor/widgets/class-alhidayah-widget-' . $file . '.php';
			$manager->register( new $class() );
		}
	}

	/**
	 * Let Elementor Pro's Theme Builder replace the header/footer if the user wants to.
	 *
	 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Manager.
	 */
	public static function locations( $manager ) {
		$manager->register_all_core_location();
	}
}
