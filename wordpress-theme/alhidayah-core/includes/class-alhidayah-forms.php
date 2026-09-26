<?php
/**
 * Contact form submissions: saved as "inquiries" under the Forms menu in the dashboard.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Forms inbox.
 */
class AlHidayah_Forms {

	const POST_TYPE = 'alhidayah_inquiry';

	/**
	 * Digits only, for tel: and wa.me links.
	 *
	 * @param string $value Phone number.
	 * @return string
	 */
	public static function digits( $value ) {
		return preg_replace( '/\D+/', '', (string) $value );
	}

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'wp_ajax_alhidayah_contact_submit', array( __CLASS__, 'submit' ) );
		add_action( 'wp_ajax_nopriv_alhidayah_contact_submit', array( __CLASS__, 'submit' ) );

		if ( is_admin() ) {
			add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
			add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
			add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( __CLASS__, 'sortable' ) );
			add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
			add_filter( 'views_edit-' . self::POST_TYPE, array( __CLASS__, 'views' ) );
			add_action( 'restrict_manage_posts', array( __CLASS__, 'filters' ) );
			add_action( 'pre_get_posts', array( __CLASS__, 'filter_query' ) );
			add_action( 'manage_posts_extra_tablenav', array( __CLASS__, 'export_button' ) );
			add_action( 'admin_post_alhidayah_inquiries_export', array( __CLASS__, 'export' ) );
			add_action( 'admin_post_alhidayah_inquiry_read', array( __CLASS__, 'toggle_read' ) );
			add_action( 'add_meta_boxes_' . self::POST_TYPE, array( __CLASS__, 'meta_boxes' ) );
			add_action( 'admin_menu', array( __CLASS__, 'menu_badge' ), 99 );
			add_filter( 'bulk_actions-edit-' . self::POST_TYPE, array( __CLASS__, 'bulk_actions' ) );
			add_filter( 'post_class', array( __CLASS__, 'row_class' ), 10, 3 );
			add_filter( 'handle_bulk_actions-edit-' . self::POST_TYPE, array( __CLASS__, 'handle_bulk' ), 10, 3 );
		}
		add_filter( 'wp_privacy_personal_data_exporters', array( __CLASS__, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( __CLASS__, 'register_eraser' ) );
	}

	/**
	 * Topics offered in the form.
	 *
	 * @return string[]
	 */
	public static function topics() {
		if ( function_exists( 'alhidayah_contact_topics' ) ) {
			return alhidayah_contact_topics();
		}
		return apply_filters(
			'alhidayah_contact_topics',
			array(
				'Order enquiry'       => __( 'Order enquiry', 'alhidayah-core' ),
				'Product advice'      => __( 'Product advice', 'alhidayah-core' ),
				'Wholesale & gifting' => __( 'Wholesale & gifting', 'alhidayah-core' ),
				'Feedback'            => __( 'Feedback', 'alhidayah-core' ),
				'Other'               => __( 'Other', 'alhidayah-core' ),
			)
		);
	}

	/**
	 * Register the private inquiry post type shown as "Forms".
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'               => __( 'Form inquiries', 'alhidayah-core' ),
					'singular_name'      => __( 'Inquiry', 'alhidayah-core' ),
					'menu_name'          => __( 'Forms', 'alhidayah-core' ),
					'all_items'          => __( 'All inquiries', 'alhidayah-core' ),
					'edit_item'          => __( 'Inquiry', 'alhidayah-core' ),
					'view_item'          => __( 'View inquiry', 'alhidayah-core' ),
					'search_items'       => __( 'Search inquiries', 'alhidayah-core' ),
					'not_found'          => __( 'No inquiries yet. Messages sent from the Contact page appear here.', 'alhidayah-core' ),
					'not_found_in_trash' => __( 'No inquiries in Trash.', 'alhidayah-core' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => false,
				'menu_position'   => 26,
				'menu_icon'       => 'dashicons-email-alt',
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'rewrite'         => false,
				'query_var'       => false,
			)
		);
	}

	/* ------------------------------------------------------------ submission */

	/**
	 * Handle a contact form submission (AJAX).
	 */
	public static function submit() {
		if ( ! check_ajax_referer( 'alhidayah_contact', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Your session expired. Please refresh the page and try again.', 'alhidayah-core' ) ), 403 );
		}

		// Spam traps: honeypot must be empty and the form must have been open for a few seconds.
		$honeypot = isset( $_POST['website'] ) ? sanitize_text_field( wp_unslash( $_POST['website'] ) ) : '';
		$started  = isset( $_POST['started'] ) ? absint( $_POST['started'] ) : 0;
		if ( '' !== $honeypot || ( $started && time() - $started < 3 ) ) {
			wp_send_json_success( array( 'stored' => false ) );
		}

		$ip     = self::client_ip();
		$bucket = 'alhidayah_contact_' . md5( $ip );
		$hits   = (int) get_transient( $bucket );
		if ( $hits >= 5 ) {
			wp_send_json_error( array( 'message' => __( 'Too many messages in a short time. Please try again in a few minutes.', 'alhidayah-core' ) ), 429 );
		}

		$fields = array(
			'name'    => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'email'   => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'phone'   => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
			'topic'   => isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '',
			'order'   => isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '',
			'message' => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
			'channel' => ( isset( $_POST['channel'] ) && 'whatsapp' === $_POST['channel'] ) ? 'whatsapp' : 'form',
		);

		$errors = array();
		if ( mb_strlen( $fields['name'] ) < 2 ) {
			$errors['name'] = __( 'Please enter your name.', 'alhidayah-core' );
		}
		if ( ! is_email( $fields['email'] ) ) {
			$errors['email'] = __( 'Please enter a valid email address.', 'alhidayah-core' );
		}
		if ( $fields['phone'] && strlen( self::digits( $fields['phone'] ) ) < 7 ) {
			$errors['phone'] = __( 'Please enter a valid phone number.', 'alhidayah-core' );
		}
		if ( mb_strlen( $fields['message'] ) < 10 ) {
			$errors['message'] = __( 'Please write a little more (at least 10 characters).', 'alhidayah-core' );
		}
		if ( $errors ) {
			wp_send_json_error( array( 'errors' => $errors ), 422 );
		}
		if ( ! array_key_exists( $fields['topic'], self::topics() ) ) {
			$fields['topic'] = 'Other';
		}
		if ( 'Order enquiry' !== $fields['topic'] ) {
			$fields['order'] = '';
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => mb_substr( $fields['name'], 0, 120 ),
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'We could not save your message. Please try again.', 'alhidayah-core' ) ), 500 );
		}
		foreach ( $fields as $key => $value ) {
			update_post_meta( $post_id, '_alhidayah_' . $key, $value );
		}
		update_post_meta( $post_id, '_alhidayah_read', 'no' );
		update_post_meta( $post_id, '_alhidayah_ip', wp_privacy_anonymize_ip( $ip ) );
		set_transient( $bucket, $hits + 1, 10 * MINUTE_IN_SECONDS );

		self::notify( $post_id, $fields );
		do_action( 'alhidayah_inquiry_received', $post_id, $fields );

		wp_send_json_success( array( 'stored' => true ) );
	}

	/**
	 * Email the store about a new inquiry.
	 *
	 * @param int   $post_id Inquiry ID.
	 * @param array $f       Fields.
	 */
	protected static function notify( $post_id, $f ) {
		$to = function_exists( 'alhidayah_opt' ) ? alhidayah_opt( 'forms_notify_email' ) : get_theme_mod( 'alhidayah_forms_notify_email', '' );
		$to = $to ? $to : get_option( 'admin_email' );
		/* translators: 1: topic, 2: name */
		$subject = sprintf( __( 'New inquiry: %1$s from %2$s', 'alhidayah-core' ), $f['topic'], $f['name'] );
		$lines   = array(
			__( 'Name', 'alhidayah-core' ) . ': ' . $f['name'],
			__( 'Email', 'alhidayah-core' ) . ': ' . $f['email'],
			__( 'Phone', 'alhidayah-core' ) . ': ' . ( $f['phone'] ? $f['phone'] : '—' ),
			__( 'Topic', 'alhidayah-core' ) . ': ' . $f['topic'],
		);
		if ( $f['order'] ) {
			$lines[] = __( 'Order number', 'alhidayah-core' ) . ': ' . $f['order'];
		}
		$lines[] = '';
		$lines[] = $f['message'];
		$lines[] = '';
		$lines[] = __( 'View in dashboard', 'alhidayah-core' ) . ': ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );
		wp_mail( $to, $subject, implode( "\n", $lines ), array( 'Reply-To: ' . $f['name'] . ' <' . $f['email'] . '>' ) );
	}

	/**
	 * Visitor IP (for rate limiting only; stored anonymised).
	 *
	 * @return string
	 */
	protected static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
	}

	/* ------------------------------------------------------------ admin list */

	/**
	 * Meta value helper.
	 *
	 * @param int    $id  Post ID.
	 * @param string $key Field.
	 * @return string
	 */
	protected static function get( $id, $key ) {
		return (string) get_post_meta( $id, '_alhidayah_' . $key, true );
	}

	/**
	 * List columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function columns( $columns ) {
		return array(
			'cb'      => $columns['cb'],
			'title'   => __( 'Name', 'alhidayah-core' ),
			'email'   => __( 'Email', 'alhidayah-core' ),
			'phone'   => __( 'Phone', 'alhidayah-core' ),
			'topic'   => __( 'Topic', 'alhidayah-core' ),
			'message' => __( 'Message', 'alhidayah-core' ),
			'channel' => __( 'Sent via', 'alhidayah-core' ),
			'date'    => __( 'Received', 'alhidayah-core' ),
		);
	}

	/**
	 * Column content.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public static function column( $column, $post_id ) {
		switch ( $column ) {
			case 'email':
				$email = self::get( $post_id, 'email' );
				printf( '<a href="%s">%s</a>', esc_url( 'mailto:' . $email ), esc_html( $email ) );
				break;
			case 'phone':
				$phone = self::get( $post_id, 'phone' );
				echo $phone ? sprintf( '<a href="%s">%s</a>', esc_url( 'tel:' . self::digits( $phone ) ), esc_html( $phone ) ) : '—';
				break;
			case 'topic':
				$order = self::get( $post_id, 'order' );
				printf( '<span class="ah-topic">%s</span>%s', esc_html( self::get( $post_id, 'topic' ) ), $order ? '<br><small>' . esc_html( sprintf( /* translators: %s order */ __( 'Order %s', 'alhidayah-core' ), $order ) ) . '</small>' : '' );
				break;
			case 'message':
				echo esc_html( wp_trim_words( self::get( $post_id, 'message' ), 16 ) );
				break;
			case 'channel':
				echo esc_html( 'whatsapp' === self::get( $post_id, 'channel' ) ? __( 'WhatsApp', 'alhidayah-core' ) : __( 'Contact form', 'alhidayah-core' ) );
				break;
		}
	}

	/**
	 * Unread rows get a class so they stand out.
	 *
	 * @param string[] $classes Classes.
	 * @param string[] $class   Extra classes.
	 * @param int      $post_id Post ID.
	 * @return string[]
	 */
	public static function row_class( $classes, $class, $post_id ) {
		if ( self::POST_TYPE === get_post_type( $post_id ) && 'no' === self::get( $post_id, 'read' ) ) {
			$classes[] = 'ah-unread';
		}
		return $classes;
	}

	/**
	 * Sortable columns.
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public static function sortable( $columns ) {
		$columns['topic'] = 'topic';
		return $columns;
	}

	/**
	 * Unread rows are bold (via post_class) — and quick actions.
	 *
	 * @param array   $actions Actions.
	 * @param WP_Post $post    Post.
	 * @return array
	 */
	public static function row_actions( $actions, $post ) {
		if ( self::POST_TYPE !== $post->post_type ) {
			return $actions;
		}
		$read = 'yes' === self::get( $post->ID, 'read' );
		$out  = array(
			'view' => sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $post->ID ) ), esc_html__( 'View', 'alhidayah-core' ) ),
			'read' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alhidayah_inquiry_read&post=' . $post->ID . '&state=' . ( $read ? 'no' : 'yes' ) ), 'alhidayah_inquiry_read_' . $post->ID ) ),
				$read ? esc_html__( 'Mark as unread', 'alhidayah-core' ) : esc_html__( 'Mark as read', 'alhidayah-core' )
			),
			'reply' => sprintf( '<a href="%s">%s</a>', esc_url( 'mailto:' . self::get( $post->ID, 'email' ) . '?subject=' . rawurlencode( 'Re: ' . self::get( $post->ID, 'topic' ) ) ), esc_html__( 'Reply', 'alhidayah-core' ) ),
		);
		if ( isset( $actions['trash'] ) ) {
			$out['trash'] = $actions['trash'];
		}
		if ( isset( $actions['untrash'] ) ) {
			$out           = array();
			$out['untrash'] = $actions['untrash'];
			$out['delete']  = $actions['delete'] ?? '';
		}
		return $out;
	}

	/**
	 * "Unread" view link.
	 *
	 * @param array $views Views.
	 * @return array
	 */
	public static function views( $views ) {
		$count = self::unread_count();
		$url   = add_query_arg(
			array(
				'post_type' => self::POST_TYPE,
				'ah_unread' => 1,
			),
			admin_url( 'edit.php' )
		);
		$current        = isset( $_GET['ah_unread'] ) ? ' class="current" aria-current="page"' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$views['unread'] = sprintf( '<a href="%s"%s>%s <span class="count">(%d)</span></a>', esc_url( $url ), $current, esc_html__( 'Unread', 'alhidayah-core' ), $count );
		return $views;
	}

	/**
	 * Topic filter dropdown.
	 *
	 * @param string $post_type Post type.
	 */
	public static function filters( $post_type ) {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}
		$current = isset( $_GET['ah_topic'] ) ? sanitize_text_field( wp_unslash( $_GET['ah_topic'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<select name="ah_topic"><option value="">' . esc_html__( 'All topics', 'alhidayah-core' ) . '</option>';
		foreach ( self::topics() as $value => $label ) {
			printf( '<option value="%s"%s>%s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $label ) );
		}
		echo '</select>';
	}

	/**
	 * Apply unread/topic filters, search in meta, sorting.
	 *
	 * @param WP_Query $query Query.
	 */
	public static function filter_query( $query ) {
		if ( ! $query->is_main_query() || self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$meta = array();
		if ( ! empty( $_GET['ah_unread'] ) ) {
			$meta[] = array(
				'key'   => '_alhidayah_read',
				'value' => 'no',
			);
		}
		if ( ! empty( $_GET['ah_topic'] ) ) {
			$meta[] = array(
				'key'   => '_alhidayah_topic',
				'value' => sanitize_text_field( wp_unslash( $_GET['ah_topic'] ) ),
			);
		}
		// phpcs:enable
		if ( $meta ) {
			$query->set( 'meta_query', $meta ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
		if ( 'topic' === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', '_alhidayah_topic' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/**
	 * Export button above the list.
	 *
	 * @param string $which Position.
	 */
	public static function export_button( $which ) {
		$screen = get_current_screen();
		if ( 'top' !== $which || ! $screen || 'edit-' . self::POST_TYPE !== $screen->id ) {
			return;
		}
		printf(
			'<div class="alignleft actions"><a class="button" href="%s">%s</a></div>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alhidayah_inquiries_export' ), 'alhidayah_inquiries_export' ) ),
			esc_html__( 'Export CSV', 'alhidayah-core' )
		);
	}

	/**
	 * Download all inquiries as CSV.
	 */
	public static function export() {
		if ( ! current_user_can( 'edit_posts' ) || ! check_admin_referer( 'alhidayah_inquiries_export' ) ) {
			wp_die( esc_html__( 'You are not allowed to export inquiries.', 'alhidayah-core' ) );
		}
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=inquiries-' . gmdate( 'Y-m-d' ) . '.csv' );
		$rows = array( array( 'Date', 'Name', 'Email', 'Phone', 'Topic', 'Order', 'Message', 'Sent via', 'Read' ) );
		foreach ( $posts as $post ) {
			$rows[] = array(
				get_the_date( 'Y-m-d H:i', $post ),
				$post->post_title,
				self::get( $post->ID, 'email' ),
				self::get( $post->ID, 'phone' ),
				self::get( $post->ID, 'topic' ),
				self::get( $post->ID, 'order' ),
				self::get( $post->ID, 'message' ),
				self::get( $post->ID, 'channel' ),
				self::get( $post->ID, 'read' ),
			);
		}
		$out = '';
		foreach ( $rows as $row ) {
			$out .= implode(
				',',
				array_map(
					static function ( $cell ) {
						$cell = (string) $cell;
						if ( preg_match( '/^[=+\-@]/', $cell ) ) {
							$cell = "'" . $cell; // Prevent spreadsheet formula injection.
						}
						return '"' . str_replace( '"', '""', $cell ) . '"';
					},
					$row
				)
			) . "\r\n";
		}
		echo "\xEF\xBB\xBF" . $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Mark one inquiry read/unread.
	 */
	public static function toggle_read() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) || ! check_admin_referer( 'alhidayah_inquiry_read_' . $post_id ) ) {
			wp_die( esc_html__( 'You are not allowed to do that.', 'alhidayah-core' ) );
		}
		update_post_meta( $post_id, '_alhidayah_read', ( isset( $_GET['state'] ) && 'yes' === $_GET['state'] ) ? 'yes' : 'no' );
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=' . self::POST_TYPE ) );
		exit;
	}

	/**
	 * Bulk read/unread.
	 *
	 * @param array $actions Actions.
	 * @return array
	 */
	public static function bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return array_merge(
			array(
				'ah_mark_read'   => __( 'Mark as read', 'alhidayah-core' ),
				'ah_mark_unread' => __( 'Mark as unread', 'alhidayah-core' ),
			),
			$actions
		);
	}

	/**
	 * Handle bulk read/unread.
	 *
	 * @param string $redirect Redirect URL.
	 * @param string $action   Action.
	 * @param int[]  $ids      Post IDs.
	 * @return string
	 */
	public static function handle_bulk( $redirect, $action, $ids ) {
		if ( ! in_array( $action, array( 'ah_mark_read', 'ah_mark_unread' ), true ) ) {
			return $redirect;
		}
		foreach ( $ids as $id ) {
			if ( current_user_can( 'edit_post', $id ) ) {
				update_post_meta( $id, '_alhidayah_read', 'ah_mark_read' === $action ? 'yes' : 'no' );
			}
		}
		return $redirect;
	}

	/**
	 * Number of unread inquiries.
	 *
	 * @return int
	 */
	public static function unread_count() {
		$query = new WP_Query(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_alhidayah_read', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'no', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		return (int) $query->found_posts;
	}

	/**
	 * Unread badge on the Forms menu.
	 */
	public static function menu_badge() {
		global $menu;
		$count = self::unread_count();
		if ( ! $count || ! is_array( $menu ) ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=' . self::POST_TYPE === $item[2] ) {
				$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>', $count ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
	}

	/* ------------------------------------------------------------ single view */

	/**
	 * Replace the editor with a read-only inquiry view.
	 *
	 * @param WP_Post $post Post.
	 */
	public static function meta_boxes( $post ) {
		remove_meta_box( 'submitdiv', self::POST_TYPE, 'side' );
		remove_meta_box( 'slugdiv', self::POST_TYPE, 'normal' );
		add_meta_box( 'alhidayah-inquiry', __( 'Message', 'alhidayah-core' ), array( __CLASS__, 'render_inquiry' ), self::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'alhidayah-inquiry-actions', __( 'Actions', 'alhidayah-core' ), array( __CLASS__, 'render_actions' ), self::POST_TYPE, 'side', 'high' );
		// Opening an inquiry marks it as read.
		if ( 'no' === self::get( $post->ID, 'read' ) ) {
			update_post_meta( $post->ID, '_alhidayah_read', 'yes' );
		}
	}

	/**
	 * Inquiry details.
	 *
	 * @param WP_Post $post Post.
	 */
	public static function render_inquiry( $post ) {
		$rows = array(
			__( 'Name', 'alhidayah-core' )     => esc_html( $post->post_title ),
			__( 'Email', 'alhidayah-core' )    => sprintf( '<a href="%s">%s</a>', esc_url( 'mailto:' . self::get( $post->ID, 'email' ) ), esc_html( self::get( $post->ID, 'email' ) ) ),
			__( 'Phone', 'alhidayah-core' )    => self::get( $post->ID, 'phone' ) ? sprintf( '<a href="%s">%s</a>', esc_url( 'tel:' . self::digits( self::get( $post->ID, 'phone' ) ) ), esc_html( self::get( $post->ID, 'phone' ) ) ) : '—',
			__( 'Topic', 'alhidayah-core' )    => esc_html( self::get( $post->ID, 'topic' ) ),
			__( 'Order', 'alhidayah-core' )    => self::get( $post->ID, 'order' ) ? esc_html( self::get( $post->ID, 'order' ) ) : '—',
			__( 'Sent via', 'alhidayah-core' ) => esc_html( 'whatsapp' === self::get( $post->ID, 'channel' ) ? __( 'WhatsApp', 'alhidayah-core' ) : __( 'Contact form', 'alhidayah-core' ) ),
			__( 'Received', 'alhidayah-core' ) => esc_html( get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $post ) ),
		);
		echo '<table class="ah-inquiry-table">';
		foreach ( $rows as $label => $value ) {
			printf( '<tr><th scope="row">%s</th><td>%s</td></tr>', esc_html( $label ), wp_kses_post( $value ) );
		}
		echo '</table>';
		echo '<div class="ah-inquiry-message">' . nl2br( esc_html( self::get( $post->ID, 'message' ) ) ) . '</div>';
	}

	/**
	 * Reply / trash actions.
	 *
	 * @param WP_Post $post Post.
	 */
	public static function render_actions( $post ) {
		$email = self::get( $post->ID, 'email' );
		$phone = self::digits( self::get( $post->ID, 'phone' ) );
		echo '<div class="ah-inquiry-actions">';
		printf( '<a class="button button-primary button-large" href="%s">%s</a>', esc_url( 'mailto:' . $email . '?subject=' . rawurlencode( 'Re: ' . self::get( $post->ID, 'topic' ) ) ), esc_html__( 'Reply by email', 'alhidayah-core' ) );
		if ( $phone ) {
			printf( '<a class="button button-large" href="%s" target="_blank" rel="noreferrer">%s</a>', esc_url( 'https://wa.me/' . $phone ), esc_html__( 'Reply on WhatsApp', 'alhidayah-core' ) );
		}
		printf( '<a class="button button-large" href="%s">%s</a>', esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alhidayah_inquiry_read&post=' . $post->ID . '&state=no' ), 'alhidayah_inquiry_read_' . $post->ID ) ), esc_html__( 'Mark as unread', 'alhidayah-core' ) );
		printf( '<a class="submitdelete" href="%s">%s</a>', esc_url( get_delete_post_link( $post->ID ) ), esc_html__( 'Move to Trash', 'alhidayah-core' ) );
		printf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=' . self::POST_TYPE ) ), esc_html__( '← All inquiries', 'alhidayah-core' ) );
		echo '</div>';
	}

	/* ------------------------------------------------------------ privacy tools */

	/**
	 * Register the personal data exporter.
	 *
	 * @param array $exporters Exporters.
	 * @return array
	 */
	public static function register_exporter( $exporters ) {
		$exporters['alhidayah-inquiries'] = array(
			'exporter_friendly_name' => __( 'Contact form inquiries', 'alhidayah-core' ),
			'callback'               => array( __CLASS__, 'privacy_export' ),
		);
		return $exporters;
	}

	/**
	 * Register the personal data eraser.
	 *
	 * @param array $erasers Erasers.
	 * @return array
	 */
	public static function register_eraser( $erasers ) {
		$erasers['alhidayah-inquiries'] = array(
			'eraser_friendly_name' => __( 'Contact form inquiries', 'alhidayah-core' ),
			'callback'             => array( __CLASS__, 'privacy_erase' ),
		);
		return $erasers;
	}

	/**
	 * Inquiries by email.
	 *
	 * @param string $email Email.
	 * @return WP_Post[]
	 */
	protected static function by_email( $email ) {
		return get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => 100,
				'post_status'    => 'any',
				'meta_key'       => '_alhidayah_email', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $email, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
	}

	/**
	 * Export a person's inquiries.
	 *
	 * @param string $email Email.
	 * @return array
	 */
	public static function privacy_export( $email ) {
		$items = array();
		foreach ( self::by_email( $email ) as $post ) {
			$data = array();
			foreach ( array( 'email', 'phone', 'topic', 'order', 'message' ) as $key ) {
				$data[] = array(
					'name'  => ucfirst( $key ),
					'value' => self::get( $post->ID, $key ),
				);
			}
			$items[] = array(
				'group_id'    => 'alhidayah-inquiries',
				'group_label' => __( 'Contact form inquiries', 'alhidayah-core' ),
				'item_id'     => 'inquiry-' . $post->ID,
				'data'        => $data,
			);
		}
		return array(
			'data' => $items,
			'done' => true,
		);
	}

	/**
	 * Erase a person's inquiries.
	 *
	 * @param string $email Email.
	 * @return array
	 */
	public static function privacy_erase( $email ) {
		$removed = false;
		foreach ( self::by_email( $email ) as $post ) {
			$removed = (bool) wp_delete_post( $post->ID, true ) || $removed;
		}
		return array(
			'items_removed'  => $removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}
}
