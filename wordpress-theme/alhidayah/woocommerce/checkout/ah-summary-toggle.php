<?php
/**
 * Mobile summary toggle total (refreshed with checkout fragments).
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;
?>
<span class="ah-summary-total"><?php echo wp_kses_post( WC()->cart->get_total() ); ?></span>
