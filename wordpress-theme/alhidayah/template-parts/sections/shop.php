<?php
/**
 * Shop: filters, search and product grid.
 *
 * @package AlHidayah
 *
 * @var array $args { eyebrow, heading, show_tools, show_guide, category, limit, guide_url }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'eyebrow'    => __( 'Our products', 'alhidayah' ),
		'heading'    => __( 'We know you love lots of<br>scents, discover them now', 'alhidayah' ),
		'show_tools' => true,
		'show_guide' => true,
		'category'   => '',
		'limit'      => 24,
		'guide_url'  => alhidayah_page_url( 'scent-guide', '' ),
		'products'   => null,
	)
);

$query = array( 'limit' => (int) $args['limit'] );
if ( $args['category'] ) {
	$query['category'] = array( $args['category'] );
}
$products = is_array( $args['products'] ) ? $args['products'] : alhidayah_products( $query );
?>
<section class="products section" id="shop" data-ah-shop>
	<div class="section-heading" data-reveal>
		<span class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<h2><?php alhidayah_inline( $args['heading'] ); ?></h2>
	</div>
	<?php if ( $args['show_tools'] ) : ?>
		<div class="shop-tools">
			<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filter fragrances', 'alhidayah' ); ?>">
				<?php foreach ( alhidayah_filters() as $index => $filter ) : ?>
					<button type="button" data-ah-filter-button="<?php echo esc_attr( $filter['id'] ); ?>" aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"<?php echo 0 === $index ? ' class="active"' : ''; ?>><?php echo esc_html( $filter['label'] ); ?></button>
				<?php endforeach; ?>
			</div>
			<label class="search"><?php alhidayah_icon( 'search', 16 ); ?><input type="search" data-ah-search placeholder="<?php esc_attr_e( 'Find your scent', 'alhidayah' ); ?>" aria-label="<?php esc_attr_e( 'Search fragrances', 'alhidayah' ); ?>"></label>
		</div>
	<?php endif; ?>
	<div class="product-grid">
		<?php
		foreach ( $products as $product ) {
			get_template_part( 'template-parts/components/product-card', null, array( 'product' => $product ) );
		}
		?>
		<?php if ( $args['show_guide'] && $products ) : ?>
			<a class="guide-card" href="<?php echo esc_url( $args['guide_url'] ? $args['guide_url'] : '#shop' ); ?>" data-ah-guide>
				<span class="eyebrow"><?php esc_html_e( 'Scent guide', 'alhidayah' ); ?></span>
				<strong><?php echo wp_kses( __( 'Not sure where<br>to begin?', 'alhidayah' ), alhidayah_inline_kses() ); ?></strong>
				<p><?php echo esc_html( sprintf( /* translators: %s: brand name */ __( 'Find the %s fragrance that matches your mood in under a minute.', 'alhidayah' ), alhidayah_opt( 'brand_name' ) ) ); ?></p>
				<span class="round-arrow"><?php alhidayah_icon( 'arrow-up-right', 20 ); ?></span>
			</a>
		<?php endif; ?>
	</div>
	<div class="empty-state"<?php echo $products ? ' hidden' : ''; ?> data-ah-empty>
		<h3><?php esc_html_e( 'No scents found.', 'alhidayah' ); ?></h3>
		<p><?php esc_html_e( 'Try a different name or note, or explore all our fragrances.', 'alhidayah' ); ?></p>
		<button class="button black" type="button" data-ah-reset><?php esc_html_e( 'View all scents', 'alhidayah' ); ?></button>
	</div>
</section>
