<?php
/**
 * Testimonials post type (admin menu "Testimonials").
 *
 * Title = customer name, content = quote, plus role, star rating and avatar colour.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Testimonials.
 */
class AlHidayah_Testimonials {

	const POST_TYPE = 'alhidayah_review';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( __CLASS__, 'save' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
	}

	/**
	 * Register the post type.
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name'          => __( 'Testimonials', 'alhidayah-core' ),
					'singular_name' => __( 'Testimonial', 'alhidayah-core' ),
					'add_new_item'  => __( 'Add testimonial', 'alhidayah-core' ),
					'edit_item'     => __( 'Edit testimonial', 'alhidayah-core' ),
					'all_items'     => __( 'All testimonials', 'alhidayah-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-format-quote',
				'menu_position' => 58,
				'supports'     => array( 'title', 'editor', 'page-attributes' ),
			)
		);
	}

	/**
	 * Name placeholder.
	 *
	 * @param string  $text Placeholder.
	 * @param WP_Post $post Post.
	 * @return string
	 */
	public static function title_placeholder( $text, $post ) {
		return self::POST_TYPE === $post->post_type ? __( 'Customer name', 'alhidayah-core' ) : $text;
	}

	/**
	 * Details meta box.
	 */
	public static function meta_box() {
		add_meta_box( 'alhidayah-review', __( 'Review details', 'alhidayah-core' ), array( __CLASS__, 'render_box' ), self::POST_TYPE, 'side' );
	}

	/**
	 * Render the details box.
	 *
	 * @param WP_Post $post Post.
	 */
	public static function render_box( $post ) {
		wp_nonce_field( 'alhidayah_review', 'alhidayah_review_nonce' );
		$role   = get_post_meta( $post->ID, '_alhidayah_role', true );
		$rating = get_post_meta( $post->ID, '_alhidayah_rating', true );
		$tone   = get_post_meta( $post->ID, '_alhidayah_tone', true );
		?>
		<p><label for="alhidayah-role"><?php esc_html_e( 'Role or city', 'alhidayah-core' ); ?></label><br><input class="widefat" id="alhidayah-role" name="alhidayah_role" value="<?php echo esc_attr( $role ); ?>"></p>
		<p><label for="alhidayah-rating"><?php esc_html_e( 'Rating (1–5, halves allowed)', 'alhidayah-core' ); ?></label><br><input class="widefat" type="number" min="1" max="5" step="0.5" id="alhidayah-rating" name="alhidayah_rating" value="<?php echo esc_attr( $rating ? $rating : 5 ); ?>"></p>
		<p><label for="alhidayah-tone"><?php esc_html_e( 'Avatar colour', 'alhidayah-core' ); ?></label><br><input type="color" id="alhidayah-tone" name="alhidayah_tone" value="<?php echo esc_attr( $tone ? $tone : '#e6d2bf' ); ?>"></p>
		<p class="description"><?php esc_html_e( 'The quote is the main content. Use menu order to sort.', 'alhidayah-core' ); ?></p>
		<?php
	}

	/**
	 * Save details.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save( $post_id ) {
		if ( ! isset( $_POST['alhidayah_review_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['alhidayah_review_nonce'] ), 'alhidayah_review' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_alhidayah_role', sanitize_text_field( wp_unslash( $_POST['alhidayah_role'] ?? '' ) ) );
		update_post_meta( $post_id, '_alhidayah_rating', min( 5, max( 1, round( (float) ( $_POST['alhidayah_rating'] ?? 5 ) * 2 ) / 2 ) ) );
		update_post_meta( $post_id, '_alhidayah_tone', sanitize_hex_color( wp_unslash( $_POST['alhidayah_tone'] ?? '' ) ) );
	}

	/**
	 * List columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		return array(
			'cb'     => $columns['cb'],
			'title'  => __( 'Customer', 'alhidayah-core' ),
			'quote'  => __( 'Quote', 'alhidayah-core' ),
			'rating' => __( 'Rating', 'alhidayah-core' ),
			'date'   => $columns['date'],
		);
	}

	/**
	 * Column content.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public static function column( $column, $post_id ) {
		if ( 'quote' === $column ) {
			echo esc_html( wp_trim_words( get_post_field( 'post_content', $post_id ), 18 ) );
		} elseif ( 'rating' === $column ) {
			echo esc_html( get_post_meta( $post_id, '_alhidayah_rating', true ) . ' / 5' );
		}
	}

	/**
	 * Published testimonials as arrays.
	 *
	 * @param int $limit Max items.
	 * @return array[]
	 */
	public static function items( $limit = 12 ) {
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => $limit,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'no_found_rows'  => true,
			)
		);
		$items = array();
		foreach ( $posts as $post ) {
			$items[] = array(
				'name'   => get_the_title( $post ),
				'role'   => (string) get_post_meta( $post->ID, '_alhidayah_role', true ),
				'rating' => (float) get_post_meta( $post->ID, '_alhidayah_rating', true ),
				'tone'   => (string) get_post_meta( $post->ID, '_alhidayah_tone', true ),
				'quote'  => wp_strip_all_tags( $post->post_content ),
			);
		}
		return $items;
	}
}
