<?php
/**
 * Product cut-out on its scent tint.
 *
 * @package AlHidayah
 *
 * @var array $args { product: array, size: card|thumb|stage, priority: bool }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'product'  => null,
		'size'     => 'card',
		'priority' => false,
	)
);
$p    = $args['product'];
if ( ! $p ) {
	return;
}
?>
<div class="bottle bottle-<?php echo esc_attr( $args['size'] ); ?>" style="--tint:<?php echo esc_attr( sanitize_hex_color( $p['tint'] ) ? $p['tint'] : '#ece6de' ); ?>">
	<img src="<?php echo esc_url( $p['cutout'] ); ?>" alt="<?php echo esc_attr( sprintf( /* translators: %1$s product, %2$s size */ __( '%1$s eau de parfum, %2$s', 'alhidayah' ), $p['name'], $p['size'] ) ); ?>" loading="<?php echo $args['priority'] ? 'eager' : 'lazy'; ?>" decoding="async" draggable="false">
</div>
