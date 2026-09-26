<?php
/**
 * Product card used on the home shop grid and the WooCommerce shop.
 *
 * @package AlHidayah
 *
 * @var array $args { product: array }
 */

defined( 'ABSPATH' ) || exit;

$p = isset( $args['product'] ) ? $args['product'] : null;
if ( ! $p ) {
	return;
}
$filters = array_merge( array( 'all' ), $p['families'] );
if ( $p['is_new'] ) {
	$filters[] = 'new';
}
if ( $p['best'] ) {
	$filters[] = 'best';
}
if ( $p['limited'] ) {
	$filters[] = 'limited';
}
$search = strtolower( implode( ' ', array_merge( array( $p['name'], $p['accords'], $p['family'] ), $p['notes']['top'], $p['notes']['middle'], $p['notes']['base'] ) ) );
?>
<article class="product-card" data-ah-card data-filters="<?php echo esc_attr( implode( ' ', $filters ) ); ?>" data-search="<?php echo esc_attr( $search ); ?>">
	<a class="product-image-button" href="<?php echo esc_url( $p['url'] ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s product name */ __( 'View %s', 'alhidayah' ), $p['name'] ) ); ?>">
		<?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p ) ); ?>
		<?php if ( $p['scene'] ) : ?>
			<img class="card-scene" src="<?php echo esc_url( $p['scene'] ); ?>" alt="" loading="lazy" decoding="async">
		<?php endif; ?>
		<?php if ( $p['is_new'] ) : ?>
			<span class="pill product-badge">• <?php esc_html_e( 'New', 'alhidayah' ); ?></span>
		<?php endif; ?>
		<?php if ( $p['limited'] ) : ?>
			<span class="pill product-badge">• <?php esc_html_e( 'Limited', 'alhidayah' ); ?></span>
		<?php endif; ?>
		<?php if ( $p['discount'] ) : ?>
			<span class="discount"><?php echo esc_html( sprintf( /* translators: %d percent */ __( '%d%% OFF', 'alhidayah' ), $p['discount'] ) ); ?></span>
		<?php endif; ?>
		<span class="quick-view"><?php esc_html_e( 'Discover scent', 'alhidayah' ); ?> <?php alhidayah_icon( 'arrow-up-right', 18 ); ?></span>
	</a>
	<div class="product-details">
		<a href="<?php echo esc_url( $p['url'] ); ?>"><h3><?php echo esc_html( $p['name'] ); ?></h3></a>
		<p><?php echo esc_html( $p['mood'] ); ?></p>
		<div class="price"><?php echo esc_html( alhidayah_price( $p['price'] ) ); ?> <?php if ( $p['regular'] ) : ?><del><?php echo esc_html( alhidayah_price( $p['regular'] ) ); ?></del><?php endif; ?></div>
		<?php if ( $p['purchasable'] ) : ?>
			<button class="quick-add icon-button" type="button" data-ah-add="<?php echo esc_attr( $p['id'] ); ?>" data-ah-name="<?php echo esc_attr( $p['name'] ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s product name */ __( 'Add %s to bag', 'alhidayah' ), $p['name'] ) ); ?>"><?php alhidayah_icon( 'plus', 18 ); ?></button>
		<?php endif; ?>
	</div>
</article>
