<?php
/**
 * Checkout footer (intentionally minimal, like a hosted checkout).
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="ah-co-footer"><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), alhidayah_opt( 'footer_copyright' ) ) ); ?></footer>
