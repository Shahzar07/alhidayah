<?php
/**
 * Single blog post.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ah-page' ); ?>>
		<div class="ah-page-inner">
			<header class="ah-page-head reveal-in">
				<span class="soft-pill"><?php echo esc_html( get_the_date() ); ?></span>
				<?php the_title( '<h1>', '</h1>' ); ?>
			</header>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="ah-featured reveal-in"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="entry-content reveal-in">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
			<footer class="ah-post-meta">
				<?php the_tags( '<span class="post-tags">', ' ', '</span>' ); ?>
				<?php the_post_navigation(); ?>
			</footer>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="ah-comments">';
		comments_template();
		echo '</div>';
	}
endwhile;

get_footer();
