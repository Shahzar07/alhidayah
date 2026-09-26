<?php
/**
 * Announcement bar.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

$alhidayah_url = alhidayah_opt( 'announcement_url' ) ? alhidayah_opt( 'announcement_url' ) : alhidayah_page_url( 'our-story', home_url( '/#story' ) );
?>
<div class="announcement">
	<?php alhidayah_icon( 'flask-conical', 12 ); ?>
	<span><?php echo esc_html( alhidayah_opt( 'announcement_text' ) ); ?></span>
	<a href="<?php echo esc_url( $alhidayah_url ); ?>"><?php echo esc_html( alhidayah_opt( 'announcement_label' ) ); ?> <?php alhidayah_icon( 'arrow-up-right', 12 ); ?></a>
</div>
