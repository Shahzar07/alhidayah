<?php
/**
 * Testimonials: two marquee rows moving in opposite directions.
 *
 * @package AlHidayah
 *
 * @var array $args { eyebrow, heading, items }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'eyebrow' => __( 'Testimonials', 'alhidayah' ),
		'heading' => __( 'What our customers say<br>about our products', 'alhidayah' ),
		'items'   => null,
	)
);

$items = is_array( $args['items'] ) ? $args['items'] : alhidayah_testimonials();
if ( ! $items ) {
	return;
}
$half = (int) ceil( count( $items ) / 2 );
$rows = count( $items ) > 3 ? array( array_slice( $items, 0, $half ), array_slice( $items, $half ) ) : array( $items );
?>
<section class="testimonials" id="reviews" aria-labelledby="reviews-title">
	<div class="section-heading" data-reveal>
		<span class="soft-pill"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<h2 id="reviews-title"><?php alhidayah_inline( $args['heading'] ); ?></h2>
	</div>
	<div class="marquee-wrap">
		<?php foreach ( $rows as $r => $row ) : ?>
			<div class="marquee<?php echo $r ? ' reverse' : ''; ?>">
				<div class="marquee-track">
					<?php foreach ( array_merge( $row, $row ) as $i => $t ) : ?>
						<div<?php echo $i >= count( $row ) ? ' aria-hidden="true"' : ''; ?>>
							<?php get_template_part( 'template-parts/components/testimonial', null, array( 'item' => $t ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
