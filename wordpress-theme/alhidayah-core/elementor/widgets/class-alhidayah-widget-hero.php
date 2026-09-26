<?php
/**
 * Hero widget.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Hero.
 */
class AlHidayah_Widget_Hero extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-hero';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Hero', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-banner';
	}

	/** @inheritDoc */
	protected function section() {
		return 'hero';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$d = alhidayah_defaults();
		$this->content_section();
		$this->text( 'title', __( 'Headline, line 1', 'alhidayah-core' ), $d['hero_title'] );
		$this->text( 'title_2', __( 'Headline, line 2', 'alhidayah-core' ), $d['hero_title_2'] );
		$this->text( 'highlight', __( 'Highlighted word', 'alhidayah-core' ), $d['hero_highlight'] );
		$this->text( 'text', __( 'Intro text', 'alhidayah-core' ), $d['hero_text'], 'textarea' );
		$this->text( 'button_label', __( 'Primary button', 'alhidayah-core' ), $d['hero_button_label'] );
		$this->url( 'button_url', __( 'Primary button link', 'alhidayah-core' ), '#collections' );
		$this->text( 'button_2_label', __( 'Secondary button', 'alhidayah-core' ), $d['hero_button_2_label'] );
		$this->url( 'button_2_url', __( 'Secondary button link (empty = Contact page)', 'alhidayah-core' ) );
		$this->end_controls_section();

		$this->start_controls_section(
			'media_section',
			array(
				'label' => __( 'Photo & product card', 'alhidayah-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->media( 'image', __( 'Photo (desktop & tablet)', 'alhidayah-core' ), __( 'Landscape, about 2000×1440. Keep the left side calm for the headline.', 'alhidayah-core' ) );
		$this->media( 'image_mobile', __( 'Photo (phones)', 'alhidayah-core' ), __( 'Portrait, about 1100×1360, product centred.', 'alhidayah-core' ) );
		$this->toggle( 'show_product', __( 'Show featured product card', 'alhidayah-core' ) );
		$products = array( '0' => __( '— Newest product —', 'alhidayah-core' ) );
		if ( alhidayah_has_wc() ) {
			foreach ( wc_get_products( array( 'limit' => 50, 'status' => 'publish' ) ) as $product ) {
				$products[ (string) $product->get_id() ] = $product->get_name();
			}
		}
		$this->add_control(
			'product_id',
			array(
				'label'     => __( 'Featured product', 'alhidayah-core' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $products,
				'default'   => '0',
				'condition' => array( 'show_product' => 'yes' ),
			)
		);
		$this->toggle( 'show_header', __( 'Show the header inside the hero', 'alhidayah-core' ) );
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'title'     => array( __( 'Headline', 'alhidayah-core' ), '.hero h1' ),
			'highlight' => array( __( 'Highlighted word', 'alhidayah-core' ), '.hero h1 span' ),
			'text'      => array( __( 'Intro text', 'alhidayah-core' ), '.hero-copy p' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		return $this->clean(
			array(
				'title'          => $this->val( $s, 'title' ),
				'title_2'        => $this->val( $s, 'title_2' ),
				'highlight'      => $this->val( $s, 'highlight' ),
				'text'           => $this->val( $s, 'text' ),
				'button_label'   => $this->val( $s, 'button_label' ),
				'button_url'     => $this->link( $s, 'button_url' ),
				'button_2_label' => $this->val( $s, 'button_2_label' ),
				'button_2_url'   => $this->link( $s, 'button_2_url' ),
				'image'          => $this->image( $s, 'image' ),
				'image_mobile'   => $this->image( $s, 'image_mobile' ),
				'product_id'     => (int) $this->val( $s, 'product_id' ),
				'show_product'   => 'yes' === $this->val( $s, 'show_product' ),
				'show_header'    => 'yes' === $this->val( $s, 'show_header' ),
			)
		);
	}
}
