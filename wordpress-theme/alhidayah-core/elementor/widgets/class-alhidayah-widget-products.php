<?php
/**
 * Product grid widget (filters, search, cards from WooCommerce).
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Products.
 */
class AlHidayah_Widget_Products extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-products';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Products', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-products';
	}

	/** @inheritDoc */
	protected function section() {
		return 'shop';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->content_section();
		$this->text( 'eyebrow', __( 'Eyebrow', 'alhidayah-core' ), __( 'Our products', 'alhidayah-core' ) );
		$this->text( 'heading', __( 'Heading', 'alhidayah-core' ), __( 'We know you love lots of<br>scents, discover them now', 'alhidayah-core' ), 'textarea' );
		$categories = array( '' => __( 'All products', 'alhidayah-core' ) );
		if ( alhidayah_has_wc() ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$categories[ $term->slug ] = $term->name;
				}
			}
		}
		$this->add_control(
			'category',
			array(
				'label'   => __( 'Show products from', 'alhidayah-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $categories,
				'default' => '',
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Maximum products', 'alhidayah-core' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 48,
				'default' => 24,
			)
		);
		$this->toggle( 'show_tools', __( 'Show filters and search', 'alhidayah-core' ) );
		$this->toggle( 'show_guide', __( 'Show “Scent guide” card', 'alhidayah-core' ) );
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'heading' => array( __( 'Heading', 'alhidayah-core' ), '.section-heading h2' ),
			'name'    => array( __( 'Product name', 'alhidayah-core' ), '.product-details h3' ),
			'price'   => array( __( 'Price', 'alhidayah-core' ), '.price' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		$args               = $this->clean(
			array(
				'eyebrow'  => $this->val( $s, 'eyebrow' ),
				'heading'  => $this->val( $s, 'heading' ),
				'category' => $this->val( $s, 'category' ),
				'limit'    => max( 1, (int) $this->val( $s, 'limit' ) ),
			)
		);
		$args['show_tools'] = 'yes' === $this->val( $s, 'show_tools' );
		$args['show_guide'] = 'yes' === $this->val( $s, 'show_guide' );
		return $args;
	}
}
