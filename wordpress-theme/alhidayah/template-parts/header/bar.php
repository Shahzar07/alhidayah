<?php
/**
 * Header pill: menu button, logo, shopping bag.
 *
 * @package AlHidayah
 *
 * @var array $args { solid: bool }
 */

defined( 'ABSPATH' ) || exit;

$args  = wp_parse_args( $args, array( 'solid' => false ) );
$count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
?>
<header class="header<?php echo $args['solid'] ? ' header-solid' : ''; ?>">
	<button class="icon-button" type="button" aria-label="<?php esc_attr_e( 'Open navigation', 'alhidayah' ); ?>" aria-controls="ah-menu" aria-expanded="false" data-ah-open="ah-menu"><?php alhidayah_icon( 'menu', 19 ); ?></button>
	<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: brand name */ __( '%s home', 'alhidayah' ), alhidayah_opt( 'brand_name' ) ) ); ?>"><?php get_template_part( 'template-parts/components/logo' ); ?></a>
	<button class="icon-button bag-button" type="button" aria-controls="ah-bag" aria-expanded="false" data-ah-open="ah-bag" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: number of items */ __( 'Open shopping bag, %d items', 'alhidayah' ), $count ) ); ?>" data-ah-bag-label="<?php esc_attr_e( 'Open shopping bag, %d items', 'alhidayah' ); ?>"><?php alhidayah_icon( 'shopping-bag', 18 ); ?><span class="bag-count" data-ah-count<?php echo $count ? '' : ' hidden'; ?>><?php echo esc_html( $count ); ?></span></button>
</header>
