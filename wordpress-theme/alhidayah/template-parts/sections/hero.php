<?php
/**
 * Hero: full-bleed amber photo, headline, buttons, featured product card.
 *
 * @package AlHidayah
 *
 * @var array $args Section settings (defaults come from the Customizer).
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'title'          => alhidayah_opt( 'hero_title' ),
		'title_2'        => alhidayah_opt( 'hero_title_2' ),
		'highlight'      => alhidayah_opt( 'hero_highlight' ),
		'text'           => alhidayah_opt( 'hero_text' ),
		'button_label'   => alhidayah_opt( 'hero_button_label' ),
		'button_url'     => alhidayah_opt( 'hero_button_url' ),
		'button_2_label' => alhidayah_opt( 'hero_button_2_label' ),
		'button_2_url'   => alhidayah_opt( 'hero_button_2_url' ) ? alhidayah_opt( 'hero_button_2_url' ) : alhidayah_contact_url(),
		'image'          => alhidayah_opt( 'hero_image' ),
		'image_mobile'   => alhidayah_opt( 'hero_image_mobile' ),
		'image_alt'      => __( 'Al-Hidayah Rozta-ul-Oud eau de parfum on warm travertine with amber silk', 'alhidayah' ),
		'product_id'     => (int) alhidayah_opt( 'hero_product' ),
		'show_product'   => true,
		'show_header'    => true,
	)
);

$image        = $args['image'] ? $args['image'] : alhidayah_asset( 'images/hero.jpg' );
$image_mobile = $args['image_mobile'] ? $args['image_mobile'] : ( $args['image'] ? $args['image'] : alhidayah_asset( 'images/hero-mobile.jpg' ) );

$featured = null;
if ( $args['show_product'] && alhidayah_has_wc() ) {
	if ( $args['product_id'] ) {
		$featured = alhidayah_product( $args['product_id'] );
	}
	if ( ! $featured ) {
		$list     = alhidayah_products( array( 'limit' => 1 ) );
		$featured = $list ? $list[0] : null;
	}
}
?>
<section class="hero" id="home">
	<picture>
		<source media="(max-width: 640px)" srcset="<?php echo esc_url( $image_mobile ); ?>" width="1100" height="1360">
		<img class="hero-photo" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $args['image_alt'] ); ?>" width="2000" height="1440" fetchpriority="high" decoding="async">
	</picture>
	<?php
	if ( $args['show_header'] ) {
		get_template_part( 'template-parts/header/bar' );
	}
	?>
	<div class="hero-copy">
		<h1><?php echo esc_html( $args['title'] ); ?><br><?php echo esc_html( $args['title_2'] ); ?> <span><?php echo esc_html( $args['highlight'] ); ?></span></h1>
		<?php if ( $args['text'] ) : ?>
			<p><?php alhidayah_inline( $args['text'] ); ?></p>
		<?php endif; ?>
		<div class="actions">
			<?php if ( $args['button_label'] ) : ?>
				<a class="button black" href="<?php echo esc_url( $args['button_url'] ); ?>"><?php echo esc_html( $args['button_label'] ); ?> <?php alhidayah_icon( 'arrow-up-right', 15 ); ?></a>
			<?php endif; ?>
			<?php if ( $args['button_2_label'] ) : ?>
				<a class="button white" href="<?php echo esc_url( $args['button_2_url'] ); ?>"><?php echo esc_html( $args['button_2_label'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( $featured ) : ?>
		<a class="hero-product" href="<?php echo esc_url( $featured['url'] ); ?>">
			<span class="pill">• <?php echo esc_html( $featured['is_new'] ? __( 'New', 'alhidayah' ) : __( 'Signature', 'alhidayah' ) ); ?></span>
			<strong><?php echo esc_html( $featured['name'] ); ?></strong>
			<span><?php echo esc_html( $featured['mood'] ); ?></span>
			<b><?php echo esc_html( alhidayah_price( $featured['price'] ) ); ?></b>
			<?php alhidayah_icon( 'arrow-up-right', 18, array( 'class' => 'product-arrow' ) ); ?>
		</a>
	<?php endif; ?>
	<a class="hero-scroll" href="#collections" aria-label="<?php esc_attr_e( 'Explore collections', 'alhidayah' ); ?>"><?php alhidayah_icon( 'arrow-down', 18 ); ?></a>
</section>
