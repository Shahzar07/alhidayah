<?php
/**
 * Collections: three portrait cards that filter the shop.
 *
 * @package AlHidayah
 *
 * @var array $args { eyebrow, heading, items: [ { title, tag, image, alt, filter } ] }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'eyebrow' => __( 'Our collections', 'alhidayah' ),
		'heading' => __( 'Discover the fragrance with<br>an unlimited collection', 'alhidayah' ),
		'items'   => array(),
	)
);

if ( ! $args['items'] ) {
	$args['items'] = alhidayah_collection_items();
}
?>
<section class="collections section" id="collections">
	<div class="section-heading" data-reveal>
		<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<h2><?php alhidayah_inline( $args['heading'] ); ?></h2>
	</div>
	<div class="collection-grid">
		<?php foreach ( $args['items'] as $item ) : ?>
			<a class="collection-card" data-reveal href="<?php echo esc_url( home_url( '/?filter=' . rawurlencode( $item['filter'] ) . '#shop' ) ); ?>" data-ah-filter="<?php echo esc_attr( $item['filter'] ); ?>">
				<img class="collection-visual" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy" decoding="async">
				<?php if ( $item['tag'] ) : ?>
					<span class="collection-tag"><?php echo esc_html( $item['tag'] ); ?></span>
				<?php endif; ?>
				<div class="collection-caption">
					<div><span><?php esc_html_e( 'THE COLLECTION', 'alhidayah' ); ?></span><h3><?php echo esc_html( $item['title'] ); ?></h3></div>
					<span class="round-arrow"><?php alhidayah_icon( 'arrow-up-right', 22 ); ?></span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>
