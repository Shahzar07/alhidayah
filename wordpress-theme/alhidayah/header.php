<?php
/**
 * Site header: document head, announcement bar and the floating header pill.
 *
 * Pages that open with the hero render the header pill inside the hero (as in the original design).
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

$alhidayah_checkout = function_exists( 'is_checkout' ) && is_checkout();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'alhidayah' ); ?></a>
<?php
if ( $alhidayah_checkout ) {
	get_template_part( 'template-parts/header/checkout' );
} else {
	if ( alhidayah_page_has_hero() ) {
		get_template_part( 'template-parts/header/announcement' );
	} else {
		echo '<div class="page-shell">';
		get_template_part( 'template-parts/header/bar', null, array( 'solid' => true ) );
		echo '</div>';
	}
}
?>
<main id="main" class="site-main">
