<?php
/**
 * One-click setup: Appearance → Alhidayah Setup.
 *
 * Imports the demo exactly as designed: product photography, categories, the five
 * fragrances as WooCommerce products, testimonials, store pages, the Elementor home
 * page, menus and WooCommerce defaults (cash on delivery, free shipping, classic
 * checkout). Safe to run more than once: existing items are updated, not duplicated.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

/**
 * Demo importer.
 */
class AlHidayah_Demo_Import {

	const PAGE = 'alhidayah-setup';
	const CORE = 'alhidayah-core/alhidayah-core.php';

	/**
	 * Is the companion plugin active?
	 *
	 * @return bool
	 */
	public static function core_active() {
		return class_exists( 'AlHidayah_Forms' );
	}

	/**
	 * Install and activate the bundled Alhidayah Core plugin.
	 */
	public static function install_core() {
		if ( ! current_user_can( 'install_plugins' ) || ! check_admin_referer( 'alhidayah_install_core' ) ) {
			wp_die( esc_html__( 'You are not allowed to install plugins.', 'alhidayah' ) );
		}
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! array_key_exists( self::CORE, get_plugins() ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
			$result   = $upgrader->install( ALHIDAYAH_DIR . '/plugins/alhidayah-core.zip' );
			if ( ! $result || is_wp_error( $result ) ) {
				wp_die( esc_html( is_wp_error( $result ) ? $result->get_error_message() : __( 'The plugin could not be installed. Upload plugins/alhidayah-core.zip from the theme package under Plugins → Add New.', 'alhidayah' ) ) );
			}
			wp_clean_plugins_cache();
		}
		$activated = activate_plugin( self::CORE );
		if ( is_wp_error( $activated ) ) {
			wp_die( esc_html( $activated->get_error_message() ) );
		}
		wp_safe_redirect( admin_url( 'themes.php?page=' . self::PAGE ) );
		exit;
	}

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_alhidayah_import', array( __CLASS__, 'handle' ) );
		add_action( 'after_switch_theme', array( __CLASS__, 'activated' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notice' ) );
		add_action( 'admin_post_alhidayah_install_core', array( __CLASS__, 'install_core' ) );
	}

	/**
	 * Remember to show the welcome notice after activation.
	 */
	public static function activated() {
		if ( ! get_option( 'alhidayah_demo_imported' ) ) {
			update_option( 'alhidayah_show_setup_notice', 1 );
		}
	}

	/**
	 * Welcome notice pointing at the setup screen.
	 */
	public static function notice() {
		$missing = ! self::core_active();
		if ( ! current_user_can( 'manage_options' ) || ( ! $missing && ! get_option( 'alhidayah_show_setup_notice' ) ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'appearance_page_' . self::PAGE === $screen->id ) {
			return;
		}
		printf(
			'<div class="notice notice-%s"><p><strong>%s</strong> %s <a class="button button-primary" href="%s">%s</a></p></div>',
			$missing ? 'warning' : 'info',
			esc_html__( 'Alhidayah:', 'alhidayah' ),
			esc_html(
				$missing
					? __( 'Install the bundled Alhidayah Core plugin to enable the Forms inbox, testimonials and Elementor widgets.', 'alhidayah' )
					: __( 'Import the demo to get the complete store (products, pages, menus, checkout) in one click.', 'alhidayah' )
			),
			esc_url( admin_url( 'themes.php?page=' . self::PAGE ) ),
			esc_html__( 'Open Alhidayah Setup', 'alhidayah' )
		);
	}

	/**
	 * Admin page.
	 */
	public static function menu() {
		add_theme_page( __( 'Alhidayah Setup', 'alhidayah' ), __( 'Alhidayah Setup', 'alhidayah' ), 'manage_options', self::PAGE, array( __CLASS__, 'render' ) );
	}

	/**
	 * Plugin status row.
	 *
	 * @param string $slug  Plugin slug.
	 * @param string $file  Main file.
	 * @param string $name  Name.
	 * @param bool   $needed Required.
	 */
	protected static function plugin_row( $slug, $file, $name, $needed, $note = '' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed = array_key_exists( $file, get_plugins() );
		$active    = is_plugin_active( $file );
		echo '<li>';
		echo '<span class="dashicons ' . ( $active ? 'dashicons-yes-alt' : 'dashicons-warning' ) . '"></span>';
		echo '<strong>' . esc_html( $name ) . '</strong> ';
		echo '<span>' . esc_html( $note ? $note : ( $needed ? __( '(required)', 'alhidayah' ) : __( '(recommended, for visual editing)', 'alhidayah' ) ) ) . '</span>';
		if ( ! $active ) {
			if ( $installed && current_user_can( 'activate_plugins' ) ) {
				printf( ' <a class="button" href="%s">%s</a>', esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $file ), 'activate-plugin_' . $file ) ), esc_html__( 'Activate', 'alhidayah' ) );
			} elseif ( self::CORE === $file && current_user_can( 'install_plugins' ) ) {
				printf( ' <a class="button button-primary" href="%s">%s</a>', esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alhidayah_install_core' ), 'alhidayah_install_core' ) ), esc_html__( 'Install & activate', 'alhidayah' ) );
			} elseif ( current_user_can( 'install_plugins' ) ) {
				printf( ' <a class="button" href="%s">%s</a>', esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug ) ), esc_html__( 'Install', 'alhidayah' ) );
			}
		}
		echo '</li>';
	}

	/**
	 * Render the setup screen.
	 */
	public static function render() {
		$done = get_option( 'alhidayah_demo_imported' );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$result = isset( $_GET['imported'] ) ? sanitize_key( $_GET['imported'] ) : '';
		?>
		<div class="wrap ah-setup">
			<h1><?php esc_html_e( 'Alhidayah Setup', 'alhidayah' ); ?></h1>
			<?php if ( 'yes' === $result ) : ?>
				<div class="notice notice-success"><p><?php esc_html_e( 'Demo imported. Your store is ready.', 'alhidayah' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View site →', 'alhidayah' ); ?></a></p></div>
			<?php elseif ( 'nowc' === $result ) : ?>
				<div class="notice notice-error"><p><?php esc_html_e( 'Please install and activate Alhidayah Core and WooCommerce first.', 'alhidayah' ); ?></p></div>
			<?php endif; ?>

			<div class="ah-setup-card ah-setup-hero">
				<img src="<?php echo esc_url( alhidayah_asset( 'images/hero-mobile.jpg' ) ); ?>" alt="">
				<div>
					<h2><?php esc_html_e( 'Your premium fragrance store in one click', 'alhidayah' ); ?></h2>
					<p><?php esc_html_e( 'The import creates everything shown in the demo, ready to edit.', 'alhidayah' ); ?></p>
				</div>
			</div>

			<div class="ah-setup-card">
				<h2><?php esc_html_e( '1. Plugins', 'alhidayah' ); ?></h2>
				<ul class="ah-setup-list">
					<?php
					self::plugin_row( 'alhidayah-core', self::CORE, 'Alhidayah Core', true, __( '(required, bundled with the theme: Forms inbox, testimonials, Elementor widgets)', 'alhidayah' ) );
					self::plugin_row( 'woocommerce', 'woocommerce/woocommerce.php', 'WooCommerce', true );
					self::plugin_row( 'elementor', 'elementor/elementor.php', 'Elementor', false );
					?>
				</ul>
			</div>

			<div class="ah-setup-card">
				<h2><?php esc_html_e( '2. Import the demo', 'alhidayah' ); ?></h2>
				<ul class="ah-setup-list">
					<li><?php esc_html_e( 'Five fragrances as WooCommerce products, with bottle cut-outs and lifestyle photos', 'alhidayah' ); ?></li>
					<li><?php esc_html_e( 'Three scent collections (product categories) with their photos', 'alhidayah' ); ?></li>
					<li><?php esc_html_e( 'Home page (built with Elementor when it is active), Contact page and 15 store pages', 'alhidayah' ); ?></li>
					<li><?php esc_html_e( 'Menu drawer and footer menus, testimonials', 'alhidayah' ); ?></li>
					<li><?php esc_html_e( 'Checkout: cash on delivery enabled, free shipping, Shopify-style checkout page', 'alhidayah' ); ?></li>
				</ul>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="alhidayah_import">
					<?php wp_nonce_field( 'alhidayah_import' ); ?>
					<button class="button button-primary button-hero" <?php disabled( ! alhidayah_has_wc() || ! self::core_active() ); ?>><?php echo esc_html( $done ? __( 'Run import again', 'alhidayah' ) : __( 'Import demo content', 'alhidayah' ) ); ?></button>
				</form>
				<?php if ( $done ) : ?>
					<p class="description"><?php echo esc_html( sprintf( /* translators: %s date */ __( 'Last imported %s. Running it again updates the demo products and pages instead of duplicating them.', 'alhidayah' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $done ) ) ); ?></p>
				<?php endif; ?>
			</div>

			<div class="ah-setup-card">
				<h2><?php esc_html_e( '3. Make it yours', 'alhidayah' ); ?></h2>
				<ul class="ah-setup-list">
					<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=alhidayah' ) ); ?>"><?php esc_html_e( 'Customize: brand, hero, contact details, footer, social links', 'alhidayah' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>"><?php esc_html_e( 'Products: prices, notes, photos (Alhidayah tab)', 'alhidayah' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=alhidayah_inquiry' ) ); ?>"><?php esc_html_e( 'Forms: contact form inquiries', 'alhidayah' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-orders' ) ); ?>"><?php esc_html_e( 'WooCommerce → Orders', 'alhidayah' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ); ?>"><?php esc_html_e( 'Payments: add Stripe, PayPal or bank transfer', 'alhidayah' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Menus', 'alhidayah' ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Run the import.
	 */
	public static function handle() {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'alhidayah_import' ) ) {
			wp_die( esc_html__( 'You are not allowed to import the demo.', 'alhidayah' ) );
		}
		if ( ! alhidayah_has_wc() || ! self::core_active() ) {
			wp_safe_redirect( admin_url( 'themes.php?page=' . self::PAGE . '&imported=nowc' ) );
			exit;
		}
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
		}
		self::import();
		wp_safe_redirect( admin_url( 'themes.php?page=' . self::PAGE . '&imported=yes' ) );
		exit;
	}

	/**
	 * Import everything (also usable from WP-CLI: wp eval 'AlHidayah_Demo_Import::import();').
	 */
	public static function import() {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$content = require ALHIDAYAH_DIR . '/demo/content.php';

		self::settings();
		$terms = self::categories( $content['categories'] );
		$ids   = self::products( $content['products'], $terms );
		self::testimonials( $content['testimonials'] );
		$pages = self::pages( $content['pages'] );
		self::menus( $pages );

		if ( ! empty( $ids['rozta-ul-oud'] ) ) {
			set_theme_mod( 'alhidayah_hero_product', $ids['rozta-ul-oud'] );
		}
		update_option( 'alhidayah_demo_imported', time() );
		delete_option( 'alhidayah_show_setup_notice' );
		flush_rewrite_rules();
	}

	/**
	 * Import a bundled image once and reuse it afterwards.
	 *
	 * @param string $path  Path relative to the theme.
	 * @param string $title Title / alt text.
	 * @return int Attachment ID.
	 */
	protected static function media( $path, $title ) {
		$map = get_option( 'alhidayah_demo_media', array() );
		if ( ! empty( $map[ $path ] ) && get_post( $map[ $path ] ) ) {
			return (int) $map[ $path ];
		}
		$source = ALHIDAYAH_DIR . '/' . $path;
		if ( ! file_exists( $source ) ) {
			return 0;
		}
		$tmp = wp_tempnam( basename( $source ) );
		copy( $source, $tmp );
		$id = media_handle_sideload(
			array(
				'name'     => 'alhidayah-' . basename( $source ),
				'tmp_name' => $tmp,
			),
			0,
			$title
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			return 0;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $title );
		$map[ $path ] = $id;
		update_option( 'alhidayah_demo_media', $map, false );
		return (int) $id;
	}

	/**
	 * Site, WooCommerce and Elementor settings.
	 */
	protected static function settings() {
		if ( ! get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
		}
		if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
			update_option( 'blogdescription', __( 'A scent of your own', 'alhidayah' ) );
		}

		// Store open to the public, classic checkout, simple billing-address shipping.
		update_option( 'woocommerce_coming_soon', 'no' );
		update_option( 'woocommerce_store_pages_only', 'no' );
		update_option( 'woocommerce_ship_to_destination', 'billing_only' );
		update_option( 'woocommerce_enable_guest_checkout', 'yes' );
		update_option( 'woocommerce_enable_checkout_login_reminder', 'yes' );
		update_option( 'woocommerce_enable_coupons', 'yes' );
		update_option( 'woocommerce_task_list_hidden', 'yes' );
		$profile = (array) get_option( 'woocommerce_onboarding_profile', array() );
		update_option( 'woocommerce_onboarding_profile', array_merge( $profile, array( 'skipped' => true ) ) );

		// Cash on delivery works everywhere without an account; add card gateways later.
		$cod = (array) get_option( 'woocommerce_cod_settings', array() );
		update_option(
			'woocommerce_cod_settings',
			array_merge(
				$cod,
				array(
					'enabled'     => 'yes',
					'title'       => __( 'Cash on delivery', 'alhidayah' ),
					'description' => __( 'Pay with cash when your order arrives.', 'alhidayah' ),
				)
			)
		);

		// Free shipping for locations without a dedicated zone.
		if ( class_exists( 'WC_Shipping_Zone' ) ) {
			$zone = new WC_Shipping_Zone( 0 );
			$has  = false;
			foreach ( $zone->get_shipping_methods() as $method ) {
				$has = $has || 'free_shipping' === $method->id;
			}
			if ( ! $has ) {
				$zone->add_shipping_method( 'free_shipping' );
			}
		}

		// Let the theme's typography and colours apply inside Elementor.
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}

	/**
	 * Product categories with collection photos.
	 *
	 * @param array $rows Categories.
	 * @return int[] slug => term ID.
	 */
	protected static function categories( $rows ) {
		$ids = array();
		foreach ( $rows as $order => $row ) {
			list( $slug, $name, $tag, $image, $alt ) = $row;
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term ) {
				$id = (int) $term->term_id;
				wp_update_term( $id, 'product_cat', array( 'description' => $alt ) );
			} else {
				$created = wp_insert_term(
					$name,
					'product_cat',
					array(
						'slug'        => $slug,
						'description' => $alt,
					)
				);
				if ( is_wp_error( $created ) ) {
					continue;
				}
				$id = (int) $created['term_id'];
			}
			$media = self::media( 'assets/images/' . $image, $name );
			update_term_meta( $id, 'thumbnail_id', $media );
			update_term_meta( $id, 'alhidayah_tag', $tag );
			update_term_meta( $id, 'order', $order );
			if ( function_exists( 'wc_set_term_order' ) ) {
				wc_set_term_order( $id, $order, 'product_cat' );
			}
			$ids[ $slug ] = $id;
		}
		return $ids;
	}

	/**
	 * Products.
	 *
	 * @param array $rows  Products.
	 * @param array $terms Category IDs.
	 * @return int[] slug => product ID.
	 */
	protected static function products( $rows, $terms ) {
		$ids = array();
		foreach ( $rows as $order => $row ) {
			$existing = get_page_by_path( $row['slug'], OBJECT, 'product' );
			$product  = $existing ? wc_get_product( $existing->ID ) : new WC_Product_Simple();
			if ( ! $product ) {
				continue;
			}
			$product->set_name( $row['name'] );
			$product->set_slug( $row['slug'] );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_menu_order( $order );
			$product->set_regular_price( $row['price'] );
			$product->set_sale_price( $row['sale'] );
			$product->set_short_description( $row['description'] );
			$product->set_description( $row['description'] . "\n\n" . $row['meta']['story'] );
			$product->set_stock_status( 'instock' );
			$product->set_manage_stock( false );
			$product->set_sold_individually( false );
			if ( ! $existing ) {
				$product->set_sku( wc_product_has_unique_sku( 0, $row['sku'] ) ? $row['sku'] : '' );
			}
			if ( isset( $terms[ $row['category'] ] ) ) {
				$product->set_category_ids( array( $terms[ $row['category'] ] ) );
			}
			$product->set_image_id( self::media( 'demo/images/' . $row['slug'] . '.webp', $row['name'] . ' eau de parfum 50 ml' ) );
			$scene = self::media( 'demo/images/' . $row['slug'] . '-scene.jpg', $row['name'] . ' eau de parfum lifestyle photo' );
			$product->set_gallery_image_ids( $scene ? array( $scene ) : array() );
			foreach ( $row['meta'] as $key => $value ) {
				$product->update_meta_data( '_alhidayah_' . $key, $value );
			}
			$product->update_meta_data( '_alhidayah_size', '50 ml' );
			$ids[ $row['slug'] ] = $product->save();
		}
		return $ids;
	}

	/**
	 * Testimonials.
	 *
	 * @param array $rows Rows.
	 */
	protected static function testimonials( $rows ) {
		if ( ! class_exists( 'AlHidayah_Testimonials' ) ) {
			return;
		}
		foreach ( $rows as $order => $row ) {
			list( $name, $role, $rating, $tone, $quote ) = $row;
			$existing = get_posts(
				array(
					'post_type'   => AlHidayah_Testimonials::POST_TYPE,
					'title'       => $name,
					'numberposts' => 1,
					'post_status' => 'any',
				)
			);
			$id       = wp_insert_post(
				array(
					'ID'           => $existing ? $existing[0]->ID : 0,
					'post_type'    => AlHidayah_Testimonials::POST_TYPE,
					'post_status'  => 'publish',
					'post_title'   => $name,
					'post_content' => $quote,
					'menu_order'   => $order,
				)
			);
			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_alhidayah_role', $role );
				update_post_meta( $id, '_alhidayah_rating', $rating );
				update_post_meta( $id, '_alhidayah_tone', $tone );
			}
		}
	}

	/**
	 * Create or update a page.
	 *
	 * @param string $slug    Slug.
	 * @param string $title   Title.
	 * @param string $content Content.
	 * @param string $template Page template.
	 * @return int
	 */
	protected static function page( $slug, $title, $content, $template = '' ) {
		$existing = get_page_by_path( $slug );
		$id       = wp_insert_post(
			array(
				'ID'           => $existing ? $existing->ID : 0,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => $content,
			)
		);
		if ( $template && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}
		return is_wp_error( $id ) ? 0 : (int) $id;
	}

	/**
	 * Home, contact, store pages and WooCommerce pages.
	 *
	 * @param array $rows Content pages.
	 * @return int[] slug => page ID.
	 */
	protected static function pages( $rows ) {
		$ids = array();
		foreach ( $rows as $slug => $row ) {
			$ids[ $slug ] = self::page( $slug, $row[0], $row[1] );
		}
		update_option( 'wp_page_for_privacy_policy', $ids['privacy-policy'] );

		$ids['contact'] = self::page( 'contact', __( 'Contact', 'alhidayah' ), '', 'page-templates/template-contact.php' );
		$ids['home']    = self::page( 'home', __( 'Home', 'alhidayah' ), '' );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		if ( did_action( 'elementor/loaded' ) ) {
			self::elementor_home( $ids['home'] );
		}

		// WooCommerce pages, using the classic shortcodes the theme's checkout design needs.
		if ( class_exists( 'WC_Install' ) ) {
			WC_Install::create_pages();
		}
		foreach ( array(
			'cart'     => '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',
			'checkout' => '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',
		) as $key => $shortcode ) {
			$page_id = wc_get_page_id( $key );
			if ( $page_id > 0 ) {
				wp_update_post(
					array(
						'ID'           => $page_id,
						'post_content' => $shortcode,
					)
				);
			}
		}
		$ids['shop'] = wc_get_page_id( 'shop' );
		return $ids;
	}

	/**
	 * Build the home page with the theme's Elementor widgets.
	 *
	 * @param int $page_id Page ID.
	 */
	protected static function elementor_home( $page_id ) {
		$elements = array();
		foreach ( array( 'hero', 'collections', 'ingredients', 'products', 'testimonials', 'closing' ) as $widget ) {
			$elements[] = array(
				'id'       => substr( md5( 'c' . $widget ), 0, 7 ),
				'elType'   => 'container',
				'isInner'  => false,
				'settings' => array(
					'_title'        => ucfirst( $widget ),
					'content_width' => 'full',
					'padding'       => array(
						'unit'     => 'px',
						'top'      => '0',
						'right'    => '0',
						'bottom'   => '0',
						'left'     => '0',
						'isLinked' => true,
					),
					'flex_gap'      => array(
						'unit'   => 'px',
						'size'   => 0,
						'column' => '0',
						'row'    => '0',
					),
				),
				'elements' => array(
					array(
						'id'         => substr( md5( 'w' . $widget ), 0, 7 ),
						'elType'     => 'widget',
						'widgetType' => 'alhidayah-' . $widget,
						'settings'   => array(),
						'elements'   => array(),
					),
				),
			);
		}
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
		update_post_meta( $page_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
		update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-full-width.php' );
		if ( class_exists( 'AlHidayah_Elementor' ) ) {
			AlHidayah_Elementor::setup_kit();
		}
	}

	/**
	 * Build a menu from items and assign it to a location.
	 *
	 * @param string $name     Menu name.
	 * @param string $location Location.
	 * @param array  $items    [ title, url, page_id, new_tab ].
	 * @return int
	 */
	protected static function menu_with( $name, $location, $items ) {
		$menu = wp_get_nav_menu_object( $name );
		$id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $name );
		foreach ( (array) wp_get_nav_menu_items( $id ) as $old ) {
			wp_delete_post( $old->ID, true );
		}
		foreach ( $items as $pos => $item ) {
			$args = array(
				'menu-item-title'    => $item[0],
				'menu-item-status'   => 'publish',
				'menu-item-position' => $pos + 1,
				'menu-item-target'   => ! empty( $item[3] ) ? '_blank' : '',
			);
			if ( ! empty( $item[2] ) ) {
				$args['menu-item-type']      = 'post_type';
				$args['menu-item-object']    = 'page';
				$args['menu-item-object-id'] = $item[2];
			} else {
				$args['menu-item-type'] = 'custom';
				$args['menu-item-url']  = $item[1];
			}
			wp_update_nav_menu_item( $id, 0, $args );
		}
		$locations              = get_theme_mod( 'nav_menu_locations', array() );
		$locations[ $location ] = $id;
		set_theme_mod( 'nav_menu_locations', $locations );
		return $id;
	}

	/**
	 * Menu drawer and footer menus.
	 *
	 * @param array $pages Page IDs.
	 */
	protected static function menus( $pages ) {
		$home = static function ( $path ) {
			return home_url( '/' . $path );
		};
		self::menu_with(
			'Menu drawer',
			'primary',
			array(
				array( 'The collection', $home( '#collections' ) ),
				array( 'Shop fragrances', $home( '#shop' ) ),
				array( 'Our ingredients', $home( '#story' ) ),
				array( 'Customer reviews', $home( '#reviews' ) ),
				array( 'Contact us', '', $pages['contact'] ),
			)
		);
		self::menu_with(
			'Shop',
			'footer-shop',
			array(
				array( 'New Arrivals', $home( '?filter=new#shop' ) ),
				array( 'Best Sellers', $home( '?filter=best#shop' ) ),
				array( 'Gift Sets', '', $pages['gift-sets'] ),
				array( 'Limited Edition', $home( '?filter=limited#shop' ) ),
				array( 'All Collections', $home( '?filter=all#shop' ) ),
			)
		);
		self::menu_with(
			'Customer Care',
			'footer-care',
			array(
				array( 'Shipping & Delivery', '', $pages['shipping-delivery'] ),
				array( 'Returns & Exchanges', '', $pages['returns-exchanges'] ),
				array( 'FAQ', '', $pages['faq'] ),
				array( 'Track Order', '', $pages['track-order'] ),
				array( 'Contact Support', '', $pages['contact'] ),
			)
		);
		self::menu_with(
			'Discover',
			'footer-discover',
			array(
				array( 'Our Story', '', $pages['our-story'] ),
				array( 'Store Locator', '', $pages['store-locator'] ),
				array( 'Ingredients & Ethics', '', $pages['ingredients-ethics'] ),
				array( 'Scent Guide', '', $pages['scent-guide'] ),
				array( 'Journal & Tips', '', $pages['journal-tips'] ),
			)
		);
		self::menu_with(
			'Legal',
			'footer-legal',
			array(
				array( 'Terms of Service', '', $pages['terms-of-service'] ),
				array( 'Privacy Policy', '', $pages['privacy-policy'] ),
				array( 'Refund Policy', '', $pages['refund-policy'] ),
				array( 'Cookie Settings', '', $pages['cookie-settings'] ),
				array( 'Accessibility', '', $pages['accessibility'] ),
			)
		);
		$connect = array();
		foreach ( alhidayah_socials() as $social ) {
			$connect[] = array( $social['label'], $social['url'], 0, true );
		}
		self::menu_with( 'Connect', 'footer-connect', $connect );
	}
}
