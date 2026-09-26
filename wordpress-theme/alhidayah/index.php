<?php
/**
 * Blog and archive listing (also the fallback template).
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="ah-page ah-archive">
	<div class="ah-page-inner ah-page-inner--wide">
		<header class="ah-page-head reveal-in">
			<span class="soft-pill"><?php esc_html_e( 'Journal', 'alhidayah' ); ?></span>
			<h1>
				<?php
				if ( is_search() ) {
					/* translators: %s search query */
					printf( esc_html__( 'Results for “%s”', 'alhidayah' ), esc_html( get_search_query() ) );
				} elseif ( is_archive() ) {
					echo wp_kses_post( get_the_archive_title() );
				} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
					echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) );
				} else {
					esc_html_e( 'Journal & tips', 'alhidayah' );
				}
				?>
			</h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?> data-reveal>
						<a class="post-card-image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) );
							}
							?>
						</a>
						<span class="eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
						<?php the_title( '<h2><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="ah-pagination"><?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?></div>
		<?php else : ?>
			<div class="empty-state">
				<h3><?php esc_html_e( 'Nothing here yet.', 'alhidayah' ); ?></h3>
				<p><?php esc_html_e( 'Try a search, or explore our fragrances.', 'alhidayah' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
