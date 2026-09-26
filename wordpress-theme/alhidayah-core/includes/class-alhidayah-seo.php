<?php
/**
 * Lightweight SEO: meta description, Open Graph and Organization schema.
 * Automatically steps aside when Yoast SEO, Rank Math or SEOPress is active.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * SEO.
 */
class AlHidayah_SEO {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'meta' ), 3 );
	}

	/**
	 * Whether a dedicated SEO plugin handles meta tags.
	 *
	 * @return bool
	 */
	protected static function plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'All_in_One_SEO_Pack' );
	}

	/**
	 * Print tags.
	 */
	public static function meta() {
		if ( self::plugin_active() ) {
			return;
		}
		$title       = wp_get_document_title();
		$description = get_bloginfo( 'description' );
		$image       = alhidayah_asset( 'images/hero.jpg' );
		$type        = 'website';
		$url         = home_url( add_query_arg( array() ) );

		if ( is_singular() ) {
			$post        = get_queried_object();
			$url         = get_permalink( $post );
			$excerpt     = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '' );
			$description = $excerpt ? $excerpt : $description;
			if ( has_post_thumbnail( $post ) ) {
				$image = get_the_post_thumbnail_url( $post, 'large' );
			}
			if ( 'product' === $post->post_type && function_exists( 'alhidayah_product' ) ) {
				$p = alhidayah_product( $post->ID );
				if ( $p ) {
					$description = $p['description'];
					$image       = $p['scene_full'] ? $p['scene_full'] : $p['cutout'];
					$type        = 'product';
				}
			} elseif ( 'post' === $post->post_type ) {
				$type = 'article';
			}
		}
		if ( is_front_page() ) {
			$description = $description ? $description : alhidayah_opt( 'announcement_text' );
			$url         = home_url( '/' );
		}
		$description = wp_strip_all_tags( (string) $description );

		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		if ( $description ) {
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		}
		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

		if ( is_front_page() ) {
			$schema = array(
				'@context' => 'https://schema.org',
				'@type'    => 'Organization',
				'name'     => alhidayah_opt( 'brand_name' ),
				'url'      => home_url( '/' ),
				'logo'     => alhidayah_asset( 'images/apple-touch-icon.png' ),
				'email'    => alhidayah_opt( 'contact_email' ),
				'sameAs'   => wp_list_pluck( alhidayah_socials(), 'url' ),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
		}
	}
}
