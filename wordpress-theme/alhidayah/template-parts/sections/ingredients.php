<?php
/**
 * Ingredients: dark band with floating botanicals.
 *
 * @package AlHidayah
 *
 * @var array $args { pill, heading, text, button_label, button_url }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'pill'         => __( 'Soulful creations. Beautiful ingredients.', 'alhidayah' ),
		'heading'      => __( 'Only high-quality<br>perfume ingredients', 'alhidayah' ),
		'text'         => __( 'We create perfumes that can be enjoyed to the fullest,<br class="desktop-br"> using ingredients whose quality is beyond doubt.', 'alhidayah' ),
		'button_label' => __( 'View our story', 'alhidayah' ),
		'button_url'   => alhidayah_page_url( 'our-story', home_url( '/' ) ),
	)
);
?>
<section class="ingredients" id="story">
	<div class="botanicals" aria-hidden="true">
		<?php for ( $i = 0; $i < 6; $i++ ) : ?>
			<div class="botanical botanical-<?php echo esc_attr( $i ); ?>"></div>
		<?php endfor; ?>
	</div>
	<div class="ingredients-copy" data-reveal>
		<span class="outline-pill"><?php echo esc_html( $args['pill'] ); ?></span>
		<h2><?php alhidayah_inline( $args['heading'] ); ?></h2>
		<p><?php alhidayah_inline( $args['text'] ); ?></p>
		<?php if ( $args['button_label'] ) : ?>
			<a class="button white" href="<?php echo esc_url( $args['button_url'] ); ?>"><?php echo esc_html( $args['button_label'] ); ?> <?php alhidayah_icon( 'arrow-up-right', 14 ); ?></a>
		<?php endif; ?>
	</div>
</section>
