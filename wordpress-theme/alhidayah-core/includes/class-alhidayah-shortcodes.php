<?php
/**
 * Shortcodes.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * [alhidayah_contact_links]: email, phone, WhatsApp and the contact page, from Appearance → Customize → Contact.
 */
class AlHidayah_Shortcodes {

	/**
	 * Register shortcodes.
	 */
	public static function init() {
		add_shortcode( 'alhidayah_contact_links', array( __CLASS__, 'contact_links' ) );
	}

	/**
	 * Contact list markup.
	 *
	 * @return string
	 */
	public static function contact_links() {
		$email = antispambot( alhidayah_opt( 'contact_email' ) );
		$phone = alhidayah_opt( 'contact_phone' );
		ob_start();
		?>
		<div class="contact-list">
			<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php alhidayah_icon( 'mail', 16 ); ?><?php echo esc_html( $email ); ?></a>
			<a href="<?php echo esc_url( 'tel:' . alhidayah_digits( $phone ) ); ?>"><?php alhidayah_icon( 'phone', 16 ); ?><?php echo esc_html( $phone ); ?></a>
			<a href="<?php echo esc_url( 'https://wa.me/' . alhidayah_whatsapp() ); ?>" target="_blank" rel="noreferrer"><?php alhidayah_icon( 'message-circle', 16 ); ?><?php esc_html_e( 'Chat on WhatsApp', 'alhidayah-core' ); ?></a>
			<a class="button black" href="<?php echo esc_url( alhidayah_contact_url() ); ?>"><?php esc_html_e( 'Open contact page', 'alhidayah-core' ); ?> <?php alhidayah_icon( 'arrow-up-right', 15 ); ?></a>
		</div>
		<?php
		return ob_get_clean();
	}
}
