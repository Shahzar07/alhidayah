<?php
/**
 * Single product page: "Discover our best collection" sheet with photo, notes table,
 * price and "I want this", plus Prev/Next through the catalogue.
 *
 * Override of woocommerce/templates/single-product.php.
 *
 * @package AlHidayah
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	global $product;
	$p = alhidayah_product( $product );
	if ( ! $p ) {
		continue;
	}

	$ids   = AlHidayah_WooCommerce::catalogue_ids();
	$index = array_search( $p['id'], $ids, true );
	$index = false === $index ? 0 : $index;
	$total = max( 1, count( $ids ) );
	$prev  = $ids ? wc_get_product( $ids[ ( $index - 1 + $total ) % $total ] ) : null;
	$next  = $ids ? wc_get_product( $ids[ ( $index + 1 ) % $total ] ) : null;
	$all   = alhidayah_products( array( 'limit' => 12 ) );

	if ( function_exists( 'WC' ) && WC()->structured_data ) {
		WC()->structured_data->generate_product_data( $product );
	}
	$family = $p['family'] ? $p['family'] . ' · ' : '';
	$max    = $product->get_max_purchase_quantity();
	$max    = $max > 0 ? min( $max, 20 ) : 20;
	?>
	<section class="product-hero" aria-labelledby="product-name">
		<?php wc_print_notices(); ?>
		<div class="product-hero-head">
			<div class="reveal-in">
				<span class="soft-pill"><?php esc_html_e( 'Our products', 'alhidayah' ); ?></span>
				<h1><?php echo wp_kses( __( 'Discover our best collection,<br>in other words, many fans', 'alhidayah' ), alhidayah_inline_kses() ); ?></h1>
				<p>
					<?php
					/* translators: %s: number of fragrances in words or digits */
					echo esc_html( sprintf( _n( '%s signature eau de parfum, composed around a single feeling.', '%s signature eau de parfums, each composed around a single feeling.', $total, 'alhidayah' ), 5 === $total ? __( 'Five', 'alhidayah' ) : number_format_i18n( $total ) ) );
					?>
					<br class="desktop-br"> <?php esc_html_e( 'Explore the scent that was made to become yours.', 'alhidayah' ); ?>
				</p>
			</div>
			<?php if ( $prev && $next && $total > 1 ) : ?>
				<div class="pager reveal-in">
					<span class="pager-count"><?php echo esc_html( sprintf( '%02d / %02d', $index + 1, $total ) ); ?></span>
					<a class="pager-button" href="<?php echo esc_url( $prev->get_permalink() ); ?>" data-ah-prev aria-label="<?php echo esc_attr( sprintf( /* translators: %s product */ __( 'Previous: %s', 'alhidayah' ), $prev->get_name() ) ); ?>"><?php alhidayah_icon( 'arrow-left', 14 ); ?> <?php esc_html_e( 'Prev', 'alhidayah' ); ?></a>
					<a class="pager-button" href="<?php echo esc_url( $next->get_permalink() ); ?>" data-ah-next aria-label="<?php echo esc_attr( sprintf( /* translators: %s product */ __( 'Next: %s', 'alhidayah' ), $next->get_name() ) ); ?>"><?php esc_html_e( 'Next', 'alhidayah' ); ?> <?php alhidayah_icon( 'arrow-right', 14 ); ?></a>
				</div>
			<?php endif; ?>
		</div>

		<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-sheet reveal-in', $product ); ?> data-ah-product>
			<div class="product-sheet-media">
				<?php if ( $p['scene'] ) : ?>
					<img class="sheet-scene" data-ah-media="scene" src="<?php echo esc_url( $p['scene'] ); ?>" alt="<?php echo esc_attr( sprintf( /* translators: %s product */ __( '%s eau de parfum lifestyle photo', 'alhidayah' ), $p['name'] ) ); ?>" fetchpriority="high">
					<div data-ah-media="bottle" hidden><?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'stage', 'priority' => true ) ); ?></div>
				<?php else : ?>
					<?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'stage', 'priority' => true ) ); ?>
				<?php endif; ?>
				<?php if ( $p['is_new'] ) : ?>
					<span class="pill product-badge">• <?php esc_html_e( 'New', 'alhidayah' ); ?></span>
				<?php endif; ?>
				<?php if ( $p['limited'] ) : ?>
					<span class="pill product-badge">• <?php esc_html_e( 'Limited edition', 'alhidayah' ); ?></span>
				<?php endif; ?>
				<?php if ( $p['discount'] ) : ?>
					<span class="discount"><?php echo esc_html( sprintf( /* translators: %d percent */ __( '%d%% OFF', 'alhidayah' ), $p['discount'] ) ); ?></span>
				<?php endif; ?>
				<?php if ( $p['scene'] ) : ?>
					<div class="media-switch" role="group" aria-label="<?php esc_attr_e( 'Product images', 'alhidayah' ); ?>">
						<button type="button" aria-pressed="true" data-ah-view="scene"><img src="<?php echo esc_url( $p['scene'] ); ?>" alt=""><span class="sr-only"><?php esc_html_e( 'Lifestyle photo', 'alhidayah' ); ?></span></button>
						<button type="button" aria-pressed="false" data-ah-view="bottle"><?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $p, 'size' => 'thumb' ) ); ?><span class="sr-only"><?php esc_html_e( 'Bottle photo', 'alhidayah' ); ?></span></button>
					</div>
				<?php endif; ?>
			</div>
			<div class="product-sheet-copy">
				<div>
					<span class="sheet-family"><?php echo esc_html( $family . sprintf( /* translators: %s size */ __( 'Eau de parfum %s', 'alhidayah' ), $p['size'] ) ); ?></span>
					<h2 id="product-name"><?php echo esc_html( $p['name'] ); ?></h2>
					<?php if ( $p['accords'] ) : ?>
						<p class="sheet-accords"><?php echo esc_html( $p['accords'] ); ?></p>
					<?php endif; ?>
				</div>
				<div class="sheet-bottom">
					<p class="sheet-description"><?php echo esc_html( $p['description'] ); ?></p>
					<dl class="note-table">
						<div><dt><?php esc_html_e( 'Base note', 'alhidayah' ); ?></dt><dd><?php echo esc_html( implode( ', ', $p['notes']['base'] ) ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Middle note', 'alhidayah' ); ?></dt><dd><?php echo esc_html( implode( ', ', $p['notes']['middle'] ) ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Top note', 'alhidayah' ); ?></dt><dd><?php echo esc_html( implode( ', ', $p['notes']['top'] ) ); ?></dd></div>
					</dl>
					<form class="sheet-buy" method="post" action="<?php echo esc_url( $p['url'] ); ?>" data-ah-add-form="<?php echo esc_attr( $p['id'] ); ?>" data-ah-name="<?php echo esc_attr( $p['name'] ); ?>">
						<div class="sheet-price">
							<strong data-ah-total data-unit="<?php echo esc_attr( $p['price'] ); ?>"><?php echo esc_html( alhidayah_price( $p['price'] ) ); ?></strong>
							<?php if ( $p['regular'] ) : ?>
								<del data-ah-was data-unit="<?php echo esc_attr( $p['regular'] ); ?>"><?php echo esc_html( alhidayah_price( $p['regular'] ) ); ?></del>
							<?php endif; ?>
						</div>
						<div class="sheet-actions">
							<?php if ( $p['purchasable'] ) : ?>
								<div class="quantity" role="group" aria-label="<?php esc_attr_e( 'Quantity', 'alhidayah' ); ?>">
									<button type="button" data-ah-step="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'alhidayah' ); ?>" disabled><?php alhidayah_icon( 'minus', 13 ); ?></button>
									<span data-ah-qty-label aria-live="polite">1</span>
									<button type="button" data-ah-step="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'alhidayah' ); ?>"><?php alhidayah_icon( 'plus', 13 ); ?></button>
									<input type="hidden" name="quantity" value="1" min="1" max="<?php echo esc_attr( $max ); ?>">
								</div>
								<button class="button black want" type="submit" name="add-to-cart" value="<?php echo esc_attr( $p['id'] ); ?>"><?php alhidayah_icon( 'shopping-bag', 15 ); ?> <?php esc_html_e( 'I want this', 'alhidayah' ); ?></button>
							<?php else : ?>
								<button class="button black want" type="button" disabled><?php esc_html_e( 'Sold out', 'alhidayah' ); ?></button>
							<?php endif; ?>
						</div>
					</form>
				</div>
			</div>
		</article>

		<?php if ( count( $all ) > 1 ) : ?>
			<div class="sheet-thumbs" role="navigation" aria-label="<?php esc_attr_e( 'All fragrances', 'alhidayah' ); ?>">
				<?php foreach ( $all as $item ) : ?>
					<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['id'] === $p['id'] ? ' class="active" aria-current="page"' : ''; ?>><?php get_template_part( 'template-parts/components/bottle', null, array( 'product' => $item, 'size' => 'thumb' ) ); ?><span><?php echo esc_html( $item['name'] ); ?></span></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( $p['story'] ) : ?>
		<section class="product-story section" data-reveal>
			<div><span class="eyebrow"><?php esc_html_e( 'The story', 'alhidayah' ); ?></span><h2><?php echo esc_html( $p['story'] ); ?></h2></div>
			<div class="wear-tips">
				<div><small>01</small><strong><?php esc_html_e( 'Where to apply', 'alhidayah' ); ?></strong><p><?php esc_html_e( 'Pulse points: wrists, neck and behind the ears. Let it settle, don’t rub.', 'alhidayah' ); ?></p></div>
				<div><small>02</small><strong><?php esc_html_e( 'When to wear', 'alhidayah' ); ?></strong><p>
					<?php
					$slugs = $p['families'];
					if ( in_array( 'soft-floral', $slugs, true ) ) {
						esc_html_e( 'Daytime, celebrations and warm evenings.', 'alhidayah' );
					} elseif ( in_array( 'warm-spicy', $slugs, true ) ) {
						esc_html_e( 'Evenings out and cooler days.', 'alhidayah' );
					} else {
						esc_html_e( 'Any time you want to leave an impression.', 'alhidayah' );
					}
					?>
				</p></div>
				<div><small>03</small><strong><?php esc_html_e( 'Longevity', 'alhidayah' ); ?></strong><p><?php esc_html_e( '6–10 hours on skin, longer on fabric.', 'alhidayah' ); ?></p></div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	alhidayah_section( 'testimonials' );
	alhidayah_section(
		'closing',
		array(
			'eyebrow'      => __( 'Keep exploring', 'alhidayah' ),
			'heading'      => __( 'Find the scent that<br>feels like <span>you</span>.', 'alhidayah' ),
			'button_label' => __( 'View all fragrances', 'alhidayah' ),
			'button_url'   => home_url( '/#shop' ),
		)
	);
endwhile;

get_footer();
