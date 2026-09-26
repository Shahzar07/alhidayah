<?php
/**
 * Site footer, menu drawer, shopping bag drawer and toast.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<?php
if ( function_exists( 'is_checkout' ) && is_checkout() ) {
	get_template_part( 'template-parts/footer/checkout' );
} else {
	get_template_part( 'template-parts/footer/site-footer' );
	get_template_part( 'template-parts/components/menu-drawer' );
	get_template_part( 'template-parts/components/bag-drawer' );
	echo '<div class="toast" role="status" aria-live="polite" data-ah-toast></div>';
}
wp_footer();
?>
</body>
</html>
