<?php
/**
 * Minimal checkout header: logo and a link back to the bag.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="ah-co-header">
	<div class="ah-co-header-inner">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s brand */ __( '%s home', 'alhidayah' ), alhidayah_opt( 'brand_name' ) ) ); ?>"><?php get_template_part( 'template-parts/components/logo' ); ?></a>
		<?php if ( function_exists( 'wc_get_cart_url' ) && ! is_order_received_page() ) : ?>
			<a class="ah-co-bag" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Back to your bag', 'alhidayah' ); ?>"><?php alhidayah_icon( 'shopping-bag', 20 ); ?></a>
		<?php endif; ?>
	</div>
</header>
