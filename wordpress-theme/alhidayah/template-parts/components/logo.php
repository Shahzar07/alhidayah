<?php
/**
 * Logo: calligraphy mark (or custom logo) and wordmark.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

$alhidayah_logo = get_theme_mod( 'custom_logo' );
?>
<span class="logo">
	<?php if ( $alhidayah_logo ) : ?>
		<?php echo wp_get_attachment_image( $alhidayah_logo, 'medium', false, array( 'class' => 'custom-mark', 'alt' => '' ) ); ?>
	<?php else : ?>
		<span class="mark" aria-hidden="true"></span>
	<?php endif; ?>
	<span class="logo-word"><?php echo esc_html( alhidayah_opt( 'brand_wordmark' ) ); ?></span>
</span>
