<?php
/**
 * Closing call to action band.
 *
 * @package AlHidayah
 *
 * @var array $args { eyebrow, heading, button_label, button_url }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'eyebrow'      => __( 'A SCENT OF YOUR OWN', 'alhidayah' ),
		'heading'      => __( 'Leave a little<br>of <span>yourself</span> everywhere.', 'alhidayah' ),
		'button_label' => __( 'Find your signature', 'alhidayah' ),
		'button_url'   => is_front_page() ? '#shop' : home_url( '/#shop' ),
	)
);
?>
<section class="closing" data-reveal>
	<div>
		<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<h2><?php alhidayah_inline( $args['heading'] ); ?></h2>
	</div>
	<?php if ( $args['button_label'] ) : ?>
		<a class="button black" href="<?php echo esc_url( $args['button_url'] ); ?>"><?php echo esc_html( $args['button_label'] ); ?> <?php alhidayah_icon( 'arrow-up-right', 16 ); ?></a>
	<?php endif; ?>
</section>
