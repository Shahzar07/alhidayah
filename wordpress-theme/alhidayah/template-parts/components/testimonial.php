<?php
/**
 * Single testimonial card.
 *
 * @package AlHidayah
 *
 * @var array $args { item: { name, role, rating, tone, quote } }
 */

defined( 'ABSPATH' ) || exit;

$t        = $args['item'];
$rating   = isset( $t['rating'] ) ? (float) $t['rating'] : 5;
$words    = preg_split( '/\s+/', trim( $t['name'] ) );
$initials = strtoupper( substr( implode( '', array_map( static function ( $w ) {
	return function_exists( 'mb_substr' ) ? mb_substr( $w, 0, 1 ) : substr( $w, 0, 1 );
}, $words ) ), 0, 2 ) );
?>
<figure class="testimonial">
	<blockquote>
		<div class="stars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s rating */ __( 'Rated %s out of 5', 'alhidayah' ), $rating ) ); ?>">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) {
				if ( $rating >= $i ) {
					alhidayah_icon( 'star', 14, array( 'fill' => 'currentColor', 'stroke_width' => 0 ) );
				} elseif ( $rating >= $i - 0.5 ) {
					echo '<span class="half">';
					alhidayah_icon( 'star', 14, array( 'stroke_width' => 1.4 ) );
					alhidayah_icon( 'star-half', 14, array( 'fill' => 'currentColor', 'stroke_width' => 0 ) );
					echo '</span>';
				} else {
					alhidayah_icon( 'star', 14, array( 'stroke_width' => 1.4, 'class' => 'empty' ) );
				}
			}
			?>
		</div>
		<p>“<?php echo esc_html( $t['quote'] ); ?>”</p>
	</blockquote>
	<figcaption>
		<span class="avatar" style="background:<?php echo esc_attr( sanitize_hex_color( $t['tone'] ) ? $t['tone'] : '#e6d2bf' ); ?>"><?php echo esc_html( $initials ); ?></span>
		<span><strong><?php echo esc_html( $t['name'] ); ?></strong><small><?php echo esc_html( $t['role'] ); ?></small></span>
	</figcaption>
</figure>
