<?php
/**
 * Template helpers shared by templates, WooCommerce overrides and Elementor widgets.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every Customizer setting.
 *
 * @return array<string,mixed>
 */
function alhidayah_defaults() {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array(
			'brand_name'           => 'Al-Hidayah',
			'brand_wordmark'       => 'AL-HIDAYAH',
			'brand_tagline'        => __( 'A scent of guidance', 'alhidayah' ),
			'announcement_text'    => __( 'A little luxury. A lasting impression.', 'alhidayah' ),
			'announcement_label'   => __( 'Discover Al-Hidayah', 'alhidayah' ),
			'announcement_url'     => '',
			'contact_email'        => 'care@alhidayah.com',
			'contact_phone'        => '+92 300 000 0000',
			'contact_whatsapp'     => '',
			'contact_support'      => '0800-HIDAYAH',
			'contact_hours'        => __( 'Mon – Sat, 10:00 – 20:00', 'alhidayah' ),
			'forms_notify_email'   => '',
			'hero_title'           => __( 'Amazing scent that', 'alhidayah' ),
			'hero_title_2'         => __( 'reflects', 'alhidayah' ),
			'hero_highlight'       => __( 'character', 'alhidayah' ),
			'hero_text'            => __( 'Al-Hidayah delivers distinctive fragrances with an elegant,<br class="desktop-br"> modern touch — made for those who stand out.', 'alhidayah' ),
			'hero_button_label'    => __( 'Discover collection', 'alhidayah' ),
			'hero_button_url'      => '#collections',
			'hero_button_2_label'  => __( 'Contact us', 'alhidayah' ),
			'hero_button_2_url'    => '',
			'hero_image'           => '',
			'hero_image_mobile'    => '',
			'hero_product'         => 0,
			'footer_headline'      => __( 'Al-Hidayah is where the<br>fragrance is found.', 'alhidayah' ),
			'footer_copyright'     => __( '© {year} All rights reserved by Al-Hidayah', 'alhidayah' ),
			'social_x'             => 'https://x.com',
			'social_instagram'     => 'https://instagram.com',
			'social_facebook'      => 'https://facebook.com',
			'social_telegram'      => 'https://telegram.org',
			'social_tiktok'        => 'https://tiktok.com',
		);
	}
	return $defaults;
}

/**
 * Read a theme setting with its default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function alhidayah_opt( $key ) {
	$defaults = alhidayah_defaults();
	$value    = get_theme_mod( 'alhidayah_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
	return ( '' === $value && isset( $defaults[ $key ] ) && ! in_array( $key, array( 'announcement_url', 'hero_button_2_url', 'hero_image', 'hero_image_mobile', 'contact_whatsapp', 'forms_notify_email' ), true ) ) ? $defaults[ $key ] : $value;
}

/**
 * URL of a file in the theme's assets folder.
 *
 * @param string $path Relative path inside /assets.
 * @return string
 */
function alhidayah_asset( $path ) {
	return ALHIDAYAH_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Tags and attributes allowed for inline SVG icons.
 *
 * @return array
 */
function alhidayah_svg_kses() {
	$shape = array(
		'd'            => true,
		'cx'           => true,
		'cy'           => true,
		'r'            => true,
		'x'            => true,
		'y'            => true,
		'x1'           => true,
		'x2'           => true,
		'y1'           => true,
		'y2'           => true,
		'rx'           => true,
		'ry'           => true,
		'width'        => true,
		'height'       => true,
		'points'       => true,
		'fill'         => true,
		'stroke-width' => true,
	);
	return array(
		'svg'      => array(
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
			'aria-hidden'     => true,
			'focusable'       => true,
		),
		'path'     => $shape,
		'circle'   => $shape,
		'line'     => $shape,
		'rect'     => $shape,
		'polyline' => $shape,
		'polygon'  => $shape,
	);
}

/**
 * Build a Lucide icon, identical to the markup lucide-react renders.
 *
 * @param string $name  Icon name (kebab-case Lucide name).
 * @param int    $size  Width and height in px.
 * @param array  $args  Optional: class, stroke_width, fill.
 * @return string
 */
function alhidayah_get_icon( $name, $size = 24, $args = array() ) {
	static $icons = null;
	if ( null === $icons ) {
		$icons = require ALHIDAYAH_DIR . '/inc/icons.php';
	}
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	$args  = wp_parse_args(
		$args,
		array(
			'class'        => '',
			'stroke_width' => 2,
			'fill'         => 'none',
		)
	);
	$inner = '';
	foreach ( $icons[ $name ] as $node ) {
		$attrs = '';
		foreach ( $node[1] as $attr => $value ) {
			$attrs .= sprintf( ' %s="%s"', esc_attr( $attr ), esc_attr( $value ) );
		}
		$inner .= sprintf( '<%1$s%2$s></%1$s>', tag_escape( $node[0] ), $attrs );
	}
	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="%2$s" stroke="currentColor" stroke-width="%3$s" stroke-linecap="round" stroke-linejoin="round" class="%4$s" aria-hidden="true">%5$s</svg>',
		absint( $size ),
		esc_attr( $args['fill'] ),
		esc_attr( $args['stroke_width'] ),
		esc_attr( trim( 'lucide lucide-' . $name . ' ' . $args['class'] ) ),
		$inner
	);
}

/**
 * Echo a Lucide icon.
 *
 * @param string $name Icon name.
 * @param int    $size Size in px.
 * @param array  $args Extra args.
 */
function alhidayah_icon( $name, $size = 24, $args = array() ) {
	echo wp_kses( alhidayah_get_icon( $name, $size, $args ), alhidayah_svg_kses() );
}

/**
 * Allowed HTML for short rich strings (headlines with line breaks and highlights).
 *
 * @return array
 */
function alhidayah_inline_kses() {
	return array(
		'br'     => array( 'class' => true ),
		'span'   => array( 'class' => true ),
		'strong' => array(),
		'em'     => array(),
	);
}

/**
 * Echo a short rich string safely.
 *
 * @param string $text Text that may contain <br>/<span>.
 */
function alhidayah_inline( $text ) {
	echo wp_kses( $text, alhidayah_inline_kses() );
}

/**
 * Digits-only phone number for tel: and wa.me links.
 *
 * @param string $phone Phone number.
 * @return string
 */
function alhidayah_digits( $phone ) {
	return preg_replace( '/\D+/', '', (string) $phone );
}

/**
 * WhatsApp number (falls back to the contact phone).
 *
 * @return string
 */
function alhidayah_whatsapp() {
	$number = alhidayah_opt( 'contact_whatsapp' );
	return alhidayah_digits( $number ? $number : alhidayah_opt( 'contact_phone' ) );
}

/**
 * URL of the Contact page (a page using the Contact template, or slug "contact").
 *
 * @return string
 */
function alhidayah_contact_url() {
	$custom = alhidayah_opt( 'hero_button_2_url' );
	$pages  = get_pages(
		array(
			'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => 'page-templates/template-contact.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'number'     => 1,
		)
	);
	if ( $pages ) {
		return get_permalink( $pages[0] );
	}
	$page = get_page_by_path( 'contact' );
	if ( $page ) {
		return get_permalink( $page );
	}
	return $custom ? $custom : home_url( '/contact/' );
}

/**
 * URL of a page by slug, with a fallback.
 *
 * @param string $slug     Page slug.
 * @param string $fallback Fallback URL.
 * @return string
 */
function alhidayah_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : ( $fallback ? $fallback : home_url( '/' . $slug . '/' ) );
}

/**
 * Whether the current page starts with the full-bleed hero (which renders the header inside it).
 *
 * @return bool
 */
function alhidayah_page_has_hero() {
	if ( is_front_page() && ! alhidayah_is_elementor_page() ) {
		return true;
	}
	if ( is_singular() && alhidayah_is_elementor_page() ) {
		$data = get_post_meta( get_queried_object_id(), '_elementor_data', true );
		return is_string( $data ) && false !== strpos( $data, '"widgetType":"alhidayah-hero"' );
	}
	return false;
}

/**
 * Whether the queried page is built with Elementor.
 *
 * @param int $post_id Optional post ID.
 * @return bool
 */
function alhidayah_is_elementor_page( $post_id = 0 ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	$post_id = $post_id ? $post_id : get_queried_object_id();
	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Render a theme section (used by templates and Elementor widgets alike).
 *
 * @param string $section Section slug in template-parts/sections.
 * @param array  $args    Section arguments.
 */
function alhidayah_section( $section, $args = array() ) {
	get_template_part( 'template-parts/sections/' . sanitize_key( $section ), null, $args );
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function alhidayah_has_wc() {
	return class_exists( 'WooCommerce' );
}

/**
 * Collect everything the design needs about a product.
 *
 * @param WC_Product|int $product Product or ID.
 * @return array|null
 */
function alhidayah_product( $product ) {
	if ( ! alhidayah_has_wc() ) {
		return null;
	}
	$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
	if ( ! $product instanceof WC_Product ) {
		return null;
	}
	$id      = $product->get_id();
	$meta    = static function ( $key ) use ( $id ) {
		return (string) get_post_meta( $id, '_alhidayah_' . $key, true );
	};
	$split   = static function ( $value ) {
		return array_values( array_filter( array_map( 'trim', explode( ',', $value ) ) ) );
	};
	$gallery = $product->get_gallery_image_ids();
	$scene   = $gallery ? (int) $gallery[0] : 0;
	$terms   = get_the_terms( $id, 'product_cat' );
	$family  = '';
	$fam_ids = array();
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			if ( 'uncategorized' !== $term->slug ) {
				$family    = $family ? $family : $term->name;
				$fam_ids[] = $term->slug;
			}
		}
	}
	$regular = (float) $product->get_regular_price();
	$price   = (float) $product->get_price();
	return array(
		'id'          => $id,
		'product'     => $product,
		'name'        => $product->get_name(),
		'url'         => $product->get_permalink(),
		'price'       => $price,
		'regular'     => $product->is_on_sale() ? $regular : 0,
		'discount'    => ( $product->is_on_sale() && $regular > 0 ) ? (int) round( ( 1 - $price / $regular ) * 100 ) : 0,
		'mood'        => $meta( 'mood' ),
		'accords'     => $meta( 'accords' ),
		'story'       => $meta( 'story' ),
		'tint'        => $meta( 'tint' ) ? $meta( 'tint' ) : '#ece6de',
		'size'        => $meta( 'size' ) ? $meta( 'size' ) : __( '50 ml', 'alhidayah' ),
		'notes'       => array(
			'top'    => $split( $meta( 'notes_top' ) ),
			'middle' => $split( $meta( 'notes_middle' ) ),
			'base'   => $split( $meta( 'notes_base' ) ),
		),
		'is_new'      => 'yes' === $meta( 'is_new' ),
		'limited'     => 'yes' === $meta( 'limited' ),
		'best'        => 'yes' === $meta( 'best_seller' ),
		'family'      => $family,
		'families'    => $fam_ids,
		'description' => wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ),
		'cutout'      => $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'large' ) : wc_placeholder_img_src( 'large' ),
		'scene'       => $scene ? wp_get_attachment_image_url( $scene, 'alhidayah-scene' ) : '',
		'scene_full'  => $scene ? wp_get_attachment_image_url( $scene, 'full' ) : '',
		'in_stock'    => $product->is_in_stock(),
		'purchasable' => $product->is_purchasable() && $product->is_in_stock(),
	);
}

/**
 * Products shown by the theme, in catalogue order.
 *
 * @param array $args Extra wc_get_products() args.
 * @return array[] Product data arrays.
 */
function alhidayah_products( $args = array() ) {
	if ( ! alhidayah_has_wc() ) {
		return array();
	}
	$products = wc_get_products(
		wp_parse_args(
			$args,
			array(
				'status'  => 'publish',
				'limit'   => 24,
				'orderby' => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'visibility' => 'catalog',
			)
		)
	);
	return array_values( array_filter( array_map( 'alhidayah_product', $products ) ) );
}

/**
 * Plain price text for a numeric amount (e.g. "$145.00").
 *
 * @param float $amount Amount.
 * @return string
 */
function alhidayah_price( $amount ) {
	return alhidayah_has_wc() ? wp_strip_all_tags( wc_price( $amount ) ) : number_format_i18n( $amount, 2 );
}

/**
 * Shop filters (All scents, each scent family, New arrivals, Best sellers, Limited edition).
 *
 * @return array[] { id, label }
 */
function alhidayah_filters() {
	$filters = array(
		array(
			'id'    => 'all',
			'label' => __( 'All scents', 'alhidayah' ),
		),
	);
	if ( alhidayah_has_wc() ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'orderby'    => 'term_order',
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$filters[] = array(
					'id'    => $term->slug,
					'label' => $term->name,
				);
			}
		}
	}
	$filters[] = array(
		'id'    => 'new',
		'label' => __( 'New arrivals', 'alhidayah' ),
	);
	$filters[] = array(
		'id'    => 'best',
		'label' => __( 'Best sellers', 'alhidayah' ),
	);
	$filters[] = array(
		'id'    => 'limited',
		'label' => __( 'Limited edition', 'alhidayah' ),
	);
	return $filters;
}

/**
 * Default collection cards, built from WooCommerce product categories.
 *
 * Each category can set its card photo (term meta "alhidayah_image", attachment ID, or the
 * WooCommerce category thumbnail) and tagline (term meta "alhidayah_tag").
 *
 * @return array[]
 */
function alhidayah_collection_items() {
	$bundled = array(
		'oud-woody'   => array( 'collections/oud.jpg', __( 'Rich & regal', 'alhidayah' ) ),
		'soft-floral' => array( 'collections/floral.jpg', __( 'Soft & expressive', 'alhidayah' ) ),
		'warm-spicy'  => array( 'collections/spicy.jpg', __( 'Bold & magnetic', 'alhidayah' ) ),
	);
	$items   = array();
	if ( alhidayah_has_wc() ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'orderby'    => 'term_order',
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
				'number'     => 3,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$image_id = (int) get_term_meta( $term->term_id, 'alhidayah_image', true );
				$image_id = $image_id ? $image_id : (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
				$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
				if ( ! $image && isset( $bundled[ $term->slug ] ) ) {
					$image = alhidayah_asset( 'images/' . $bundled[ $term->slug ][0] );
				}
				$tag     = (string) get_term_meta( $term->term_id, 'alhidayah_tag', true );
				$items[] = array(
					'title'  => $term->name,
					'tag'    => $tag ? $tag : ( isset( $bundled[ $term->slug ] ) ? $bundled[ $term->slug ][1] : '' ),
					'image'  => $image ? $image : alhidayah_asset( 'images/collections/oud.jpg' ),
					'alt'    => $term->description ? wp_strip_all_tags( $term->description ) : $term->name,
					'filter' => $term->slug,
				);
			}
		}
	}
	if ( ! $items ) {
		foreach ( $bundled as $slug => $data ) {
			$items[] = array(
				'title'  => ucwords( str_replace( '-', ' & ', $slug ) ),
				'tag'    => $data[1],
				'image'  => alhidayah_asset( 'images/' . $data[0] ),
				'alt'    => '',
				'filter' => $slug,
			);
		}
	}
	return $items;
}

/**
 * Default footer links shown until a menu is assigned to a footer location.
 *
 * @param string $location Menu location.
 * @return array[] [ label, url, external ]
 */
function alhidayah_default_footer_links( $location ) {
	$home = static function ( $filter ) {
		return home_url( '/?filter=' . $filter . '#shop' );
	};
	switch ( $location ) {
		case 'footer-shop':
			return array(
				array( __( 'New Arrivals', 'alhidayah' ), $home( 'new' ) ),
				array( __( 'Best Sellers', 'alhidayah' ), $home( 'best' ) ),
				array( __( 'Gift Sets', 'alhidayah' ), alhidayah_page_url( 'gift-sets' ) ),
				array( __( 'Limited Edition', 'alhidayah' ), $home( 'limited' ) ),
				array( __( 'All Collections', 'alhidayah' ), $home( 'all' ) ),
			);
		case 'footer-care':
			return array(
				array( __( 'Shipping & Delivery', 'alhidayah' ), alhidayah_page_url( 'shipping-delivery' ) ),
				array( __( 'Returns & Exchanges', 'alhidayah' ), alhidayah_page_url( 'returns-exchanges' ) ),
				array( __( 'FAQ', 'alhidayah' ), alhidayah_page_url( 'faq' ) ),
				array( __( 'Track Order', 'alhidayah' ), alhidayah_page_url( 'track-order' ) ),
				array( __( 'Contact Support', 'alhidayah' ), alhidayah_contact_url() ),
			);
		case 'footer-discover':
			return array(
				array( __( 'Our Story', 'alhidayah' ), alhidayah_page_url( 'our-story' ) ),
				array( __( 'Store Locator', 'alhidayah' ), alhidayah_page_url( 'store-locator' ) ),
				array( __( 'Ingredients & Ethics', 'alhidayah' ), alhidayah_page_url( 'ingredients-ethics' ) ),
				array( __( 'Scent Guide', 'alhidayah' ), alhidayah_page_url( 'scent-guide' ) ),
				array( __( 'Journal & Tips', 'alhidayah' ), alhidayah_page_url( 'journal-tips' ) ),
			);
		case 'footer-legal':
			return array(
				array( __( 'Terms of Service', 'alhidayah' ), alhidayah_page_url( 'terms-of-service' ) ),
				array( __( 'Privacy Policy', 'alhidayah' ), get_privacy_policy_url() ? get_privacy_policy_url() : alhidayah_page_url( 'privacy-policy' ) ),
				array( __( 'Refund Policy', 'alhidayah' ), alhidayah_page_url( 'refund-policy' ) ),
				array( __( 'Cookie Settings', 'alhidayah' ), alhidayah_page_url( 'cookie-settings' ) ),
				array( __( 'Accessibility', 'alhidayah' ), alhidayah_page_url( 'accessibility' ) ),
			);
		default:
			$links = array();
			foreach ( alhidayah_socials() as $social ) {
				$links[] = array( $social['label'], $social['url'], true );
			}
			return $links;
	}
}

/**
 * Social profiles from the Customizer.
 *
 * @return array[] { label, url }
 */
function alhidayah_socials() {
	$map   = array(
		'x'         => __( 'X (Twitter)', 'alhidayah' ),
		'instagram' => __( 'Instagram', 'alhidayah' ),
		'facebook'  => __( 'Facebook', 'alhidayah' ),
		'telegram'  => __( 'Telegram', 'alhidayah' ),
		'tiktok'    => __( 'Tik Tok', 'alhidayah' ),
	);
	$items = array();
	foreach ( $map as $key => $label ) {
		$url = alhidayah_opt( 'social_' . $key );
		if ( $url ) {
			$items[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}
	}
	return $items;
}

/**
 * Contact form topics (filterable). The Alhidayah Core plugin validates submissions against this list.
 *
 * @return array value => label
 */
function alhidayah_contact_topics() {
	return apply_filters(
		'alhidayah_contact_topics',
		array(
			'Order enquiry'       => __( 'Order enquiry', 'alhidayah' ),
			'Product advice'      => __( 'Product advice', 'alhidayah' ),
			'Wholesale & gifting' => __( 'Wholesale & gifting', 'alhidayah' ),
			'Feedback'            => __( 'Feedback', 'alhidayah' ),
			'Other'               => __( 'Other', 'alhidayah' ),
		)
	);
}

/**
 * Testimonials: the ones managed in Testimonials (Alhidayah Core), or the demo set until any exist.
 *
 * @param int $limit Maximum items.
 * @return array[] name, role, rating, tone, quote.
 */
function alhidayah_testimonials( $limit = 12 ) {
	$items = class_exists( 'AlHidayah_Testimonials' ) ? AlHidayah_Testimonials::items( $limit ) : array();
	if ( ! $items ) {
		$content = require ALHIDAYAH_DIR . '/demo/content.php';
		foreach ( array_slice( $content['testimonials'], 0, $limit ) as $row ) {
			$items[] = array_combine( array( 'name', 'role', 'rating', 'tone', 'quote' ), $row );
		}
	}
	return $items;
}
