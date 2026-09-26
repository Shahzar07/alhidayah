<?php
/**
 * Menu drawer (left): menu links, product thumbnails, tagline.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

$links     = array();
$locations = get_nav_menu_locations();
if ( ! empty( $locations['primary'] ) ) {
	foreach ( (array) wp_get_nav_menu_items( $locations['primary'] ) as $item ) {
		if ( ! $item->menu_item_parent ) {
			$links[] = array( $item->title, $item->url );
		}
	}
}
if ( ! $links ) {
	$links = array(
		array( __( 'The collection', 'alhidayah' ), home_url( '/#collections' ) ),
		array( __( 'Shop fragrances', 'alhidayah' ), home_url( '/#shop' ) ),
		array( __( 'Our ingredients', 'alhidayah' ), home_url( '/#story' ) ),
		array( __( 'Customer reviews', 'alhidayah' ), home_url( '/#reviews' ) ),
		array( __( 'Contact us', 'alhidayah' ), alhidayah_contact_url() ),
	);
}
$products = alhidayah_products( array( 'limit' => 5 ) );
?>
<div class="ah-dialog" id="ah-menu" hidden>
	<div class="overlay" data-ah-close></div>
	<div class="drawer menu-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Navigation', 'alhidayah' ); ?>" tabindex="-1" data-lenis-prevent>
		<button class="icon-button close" type="button" aria-label="<?php esc_attr_e( 'Close', 'alhidayah' ); ?>" data-ah-close><?php alhidayah_icon( 'x', 21 ); ?></button>
		<a class="menu-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php get_template_part( 'template-parts/components/logo' ); ?></a>
		<nav aria-label="<?php esc_attr_e( 'Main', 'alhidayah' ); ?>">
			<?php foreach ( $links as $i => $link ) : ?>
				<a href="<?php echo esc_url( $link[1] ); ?>" data-ah-close-link><small><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></small><?php echo esc_html( $link[0] ); ?><?php alhidayah_icon( 'arrow-up-right', 24 ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php if ( $products ) : ?>
			<div class="menu-products">
				<?php foreach ( $products as $p ) : ?>
					<a href="<?php echo esc_url( $p['url'] ); ?>"><?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'thumb' ) ); ?><span><?php echo esc_html( $p['name'] ); ?></span></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<p><?php echo esc_html( alhidayah_opt( 'brand_tagline' ) ); ?>.</p>
	</div>
</div>
