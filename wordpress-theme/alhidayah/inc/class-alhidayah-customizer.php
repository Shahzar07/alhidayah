<?php
/**
 * Customizer settings (Appearance → Customize → Al-Hidayah).
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customizer.
 */
class AlHidayah_Customizer {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'register' ) );
	}

	/**
	 * Sanitize short rich text (allows <br> and <span>).
	 *
	 * @param string $value Value.
	 * @return string
	 */
	public static function sanitize_inline( $value ) {
		return wp_kses( $value, alhidayah_inline_kses() );
	}

	/**
	 * Sanitize a link that may be a relative anchor like "#collections".
	 *
	 * @param string $value Value.
	 * @return string
	 */
	public static function sanitize_link( $value ) {
		$value = trim( (string) $value );
		return ( '' !== $value && '#' === $value[0] ) ? '#' . sanitize_title( substr( $value, 1 ) ) : esc_url_raw( $value );
	}

	/**
	 * Register panel, sections, settings and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Manager.
	 */
	public static function register( $wp_customize ) {
		$wp_customize->add_panel(
			'alhidayah',
			array(
				'title'    => __( 'Al-Hidayah', 'alhidayah' ),
				'priority' => 30,
			)
		);

		$sections = array(
			'alhidayah_brand'   => __( 'Brand & announcement bar', 'alhidayah' ),
			'alhidayah_hero'    => __( 'Home hero', 'alhidayah' ),
			'alhidayah_contact' => __( 'Contact details', 'alhidayah' ),
			'alhidayah_footer'  => __( 'Footer & social links', 'alhidayah' ),
		);
		foreach ( $sections as $id => $title ) {
			$wp_customize->add_section(
				$id,
				array(
					'title' => $title,
					'panel' => 'alhidayah',
				)
			);
		}

		$products = array( 0 => __( '— Newest product —', 'alhidayah' ) );
		if ( alhidayah_has_wc() ) {
			foreach ( wc_get_products( array( 'limit' => 50, 'status' => 'publish' ) ) as $product ) {
				$products[ $product->get_id() ] = $product->get_name();
			}
		}

		$fields = array(
			array( 'brand_name', 'alhidayah_brand', __( 'Brand name', 'alhidayah' ), 'text' ),
			array( 'brand_wordmark', 'alhidayah_brand', __( 'Header wordmark', 'alhidayah' ), 'text' ),
			array( 'brand_tagline', 'alhidayah_brand', __( 'Tagline (menu drawer)', 'alhidayah' ), 'text' ),
			array( 'announcement_text', 'alhidayah_brand', __( 'Announcement text', 'alhidayah' ), 'text' ),
			array( 'announcement_label', 'alhidayah_brand', __( 'Announcement link label', 'alhidayah' ), 'text' ),
			array( 'announcement_url', 'alhidayah_brand', __( 'Announcement link URL (defaults to Our Story)', 'alhidayah' ), 'link' ),
			array( 'hero_title', 'alhidayah_hero', __( 'Headline, line 1', 'alhidayah' ), 'text' ),
			array( 'hero_title_2', 'alhidayah_hero', __( 'Headline, line 2', 'alhidayah' ), 'text' ),
			array( 'hero_highlight', 'alhidayah_hero', __( 'Highlighted word', 'alhidayah' ), 'text' ),
			array( 'hero_text', 'alhidayah_hero', __( 'Intro text', 'alhidayah' ), 'inline' ),
			array( 'hero_button_label', 'alhidayah_hero', __( 'Primary button label', 'alhidayah' ), 'text' ),
			array( 'hero_button_url', 'alhidayah_hero', __( 'Primary button link', 'alhidayah' ), 'link' ),
			array( 'hero_button_2_label', 'alhidayah_hero', __( 'Secondary button label', 'alhidayah' ), 'text' ),
			array( 'hero_button_2_url', 'alhidayah_hero', __( 'Secondary button link (defaults to Contact page)', 'alhidayah' ), 'link' ),
			array( 'hero_image', 'alhidayah_hero', __( 'Hero photo (desktop & tablet)', 'alhidayah' ), 'image' ),
			array( 'hero_image_mobile', 'alhidayah_hero', __( 'Hero photo (phones, portrait)', 'alhidayah' ), 'image' ),
			array( 'hero_product', 'alhidayah_hero', __( 'Featured product card', 'alhidayah' ), 'product' ),
			array( 'contact_email', 'alhidayah_contact', __( 'Email', 'alhidayah' ), 'email' ),
			array( 'contact_phone', 'alhidayah_contact', __( 'Phone', 'alhidayah' ), 'text' ),
			array( 'contact_whatsapp', 'alhidayah_contact', __( 'WhatsApp number (defaults to phone)', 'alhidayah' ), 'text' ),
			array( 'contact_support', 'alhidayah_contact', __( 'Support line', 'alhidayah' ), 'text' ),
			array( 'contact_hours', 'alhidayah_contact', __( 'Opening hours', 'alhidayah' ), 'text' ),
			array( 'forms_notify_email', 'alhidayah_contact', __( 'Send new inquiries to (defaults to site admin email)', 'alhidayah' ), 'email' ),
			array( 'footer_headline', 'alhidayah_footer', __( 'Footer headline', 'alhidayah' ), 'inline' ),
			array( 'footer_copyright', 'alhidayah_footer', __( 'Copyright ({year} is replaced)', 'alhidayah' ), 'text' ),
			array( 'social_x', 'alhidayah_footer', __( 'X (Twitter) URL', 'alhidayah' ), 'url' ),
			array( 'social_instagram', 'alhidayah_footer', __( 'Instagram URL', 'alhidayah' ), 'url' ),
			array( 'social_facebook', 'alhidayah_footer', __( 'Facebook URL', 'alhidayah' ), 'url' ),
			array( 'social_telegram', 'alhidayah_footer', __( 'Telegram URL', 'alhidayah' ), 'url' ),
			array( 'social_tiktok', 'alhidayah_footer', __( 'TikTok URL', 'alhidayah' ), 'url' ),
		);

		$defaults = alhidayah_defaults();
		foreach ( $fields as $field ) {
			list( $key, $section, $label, $type ) = $field;
			$sanitize = array(
				'text'    => 'sanitize_text_field',
				'inline'  => array( __CLASS__, 'sanitize_inline' ),
				'link'    => array( __CLASS__, 'sanitize_link' ),
				'url'     => 'esc_url_raw',
				'email'   => 'sanitize_email',
				'image'   => 'esc_url_raw',
				'product' => 'absint',
			);
			$wp_customize->add_setting(
				'alhidayah_' . $key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $sanitize[ $type ],
				)
			);
			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						'alhidayah_' . $key,
						array(
							'label'   => $label,
							'section' => $section,
						)
					)
				);
				continue;
			}
			$wp_customize->add_control(
				'alhidayah_' . $key,
				array(
					'label'   => $label,
					'section' => $section,
					'type'    => 'product' === $type ? 'select' : ( 'inline' === $type ? 'textarea' : ( 'link' === $type ? 'text' : $type ) ),
					'choices' => 'product' === $type ? $products : array(),
				)
			);
		}
	}
}
