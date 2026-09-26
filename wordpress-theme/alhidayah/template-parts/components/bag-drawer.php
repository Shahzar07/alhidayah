<?php
/**
 * Shopping bag drawer (right). Its body is rendered by bag-body.php and refreshed over AJAX.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

if ( ! alhidayah_has_wc() ) {
	return;
}
?>
<div class="ah-dialog" id="ah-bag" hidden>
	<div class="overlay" data-ah-close></div>
	<div class="drawer bag-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Your shopping bag', 'alhidayah' ); ?>" tabindex="-1" data-lenis-prevent>
		<button class="icon-button close" type="button" aria-label="<?php esc_attr_e( 'Close', 'alhidayah' ); ?>" data-ah-close><?php alhidayah_icon( 'x', 21 ); ?></button>
		<div class="bag-body" data-ah-bag-body><?php get_template_part( 'template-parts/components/bag-body' ); ?></div>
	</div>
</div>
