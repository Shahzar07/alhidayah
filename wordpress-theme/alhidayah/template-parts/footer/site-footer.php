<?php
/**
 * Footer: amber glow, headline, contact details, five link columns, giant wordmark.
 *
 * Columns come from the Footer menu locations (Appearance → Menus). Until a menu is
 * assigned, sensible default links are shown.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

$columns = array(
	'footer-shop'     => __( 'Shop', 'alhidayah' ),
	'footer-care'     => __( 'Customer Care', 'alhidayah' ),
	'footer-discover' => __( 'Discover', 'alhidayah' ),
	'footer-legal'    => __( 'Legal', 'alhidayah' ),
	'footer-connect'  => __( 'Connect', 'alhidayah' ),
);
$locations = get_nav_menu_locations();
$phone     = alhidayah_opt( 'contact_phone' );
$copyright = str_replace( '{year}', gmdate( 'Y' ), alhidayah_opt( 'footer_copyright' ) );
?>
<footer class="site-footer">
	<div class="footer-glow">
		<div class="footer-inner">
			<div class="footer-top">
				<h2 data-reveal><?php alhidayah_inline( alhidayah_opt( 'footer_headline' ) ); ?></h2>
				<ul class="footer-contact">
					<li><?php alhidayah_icon( 'mail', 14 ); ?><a href="<?php echo esc_url( 'mailto:' . antispambot( alhidayah_opt( 'contact_email' ) ) ); ?>"><?php echo esc_html( antispambot( alhidayah_opt( 'contact_email' ) ) ); ?></a></li>
					<li><?php alhidayah_icon( 'phone', 14 ); ?><a href="<?php echo esc_url( 'tel:' . alhidayah_digits( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
					<li><?php alhidayah_icon( 'headset', 14 ); ?><a href="<?php echo esc_url( alhidayah_contact_url() ); ?>"><?php echo esc_html( alhidayah_opt( 'contact_support' ) ); ?></a></li>
				</ul>
			</div>
			<nav class="footer-columns" aria-label="<?php esc_attr_e( 'Footer', 'alhidayah' ); ?>">
				<?php foreach ( $columns as $location => $fallback_title ) : ?>
					<?php
					$menu  = ! empty( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : null;
					$title = $menu ? $menu->name : $fallback_title;
					?>
					<div>
						<h3><?php echo esc_html( $title ); ?></h3>
						<?php
						if ( $menu ) {
							wp_nav_menu(
								array(
									'theme_location' => $location,
									'container'      => false,
									'items_wrap'     => '<ul>%3$s</ul>',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
						} else {
							echo '<ul>';
							foreach ( alhidayah_default_footer_links( $location ) as $link ) {
								printf(
									'<li><a href="%s"%s>%s</a></li>',
									esc_url( $link[1] ),
									! empty( $link[2] ) ? ' target="_blank" rel="noreferrer"' : '',
									esc_html( $link[0] )
								);
							}
							echo '</ul>';
						}
						?>
					</div>
				<?php endforeach; ?>
			</nav>
		</div>
		<p class="footer-copy"><?php echo esc_html( $copyright ); ?></p>
	</div>
	<div class="footer-giant" aria-hidden="true"><svg viewBox="0 0 1000 138" preserveAspectRatio="xMidYMax meet"><text x="0" y="146" textLength="1000" lengthAdjust="spacing"><?php echo esc_html( alhidayah_opt( 'brand_wordmark' ) ); ?></text></svg></div>
</footer>
