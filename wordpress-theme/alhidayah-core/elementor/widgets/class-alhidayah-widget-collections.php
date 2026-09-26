<?php
/**
 * Collections widget.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Collections.
 */
class AlHidayah_Widget_Collections extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-collections';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Collections', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/** @inheritDoc */
	protected function section() {
		return 'collections';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->content_section();
		$this->text( 'eyebrow', __( 'Eyebrow', 'alhidayah-core' ), __( 'Our collections', 'alhidayah-core' ) );
		$this->text( 'heading', __( 'Heading', 'alhidayah-core' ), __( 'Discover the fragrance with<br>an unlimited collection', 'alhidayah-core' ), 'textarea' );

		$repeater = new Repeater();
		$repeater->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'alhidayah-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Oud & woody', 'alhidayah-core' ),
			)
		);
		$repeater->add_control(
			'tag',
			array(
				'label'   => __( 'Tag', 'alhidayah-core' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Rich & regal', 'alhidayah-core' ),
			)
		);
		$repeater->add_control(
			'image',
			array(
				'label' => __( 'Photo (portrait 4:5)', 'alhidayah-core' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'filter',
			array(
				'label'       => __( 'Product category slug to show', 'alhidayah-core' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'oud-woody',
				'description' => __( 'Also accepts: all, new, best, limited.', 'alhidayah-core' ),
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Cards', 'alhidayah-core' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ title }}}',
				'description' => __( 'Leave empty to show your product categories automatically (with their category images).', 'alhidayah-core' ),
			)
		);
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'heading' => array( __( 'Heading', 'alhidayah-core' ), '.section-heading h2' ),
			'card'    => array( __( 'Card title', 'alhidayah-core' ), '.collection-caption h3' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		$items = array();
		foreach ( (array) ( $s['items'] ?? array() ) as $item ) {
			$items[] = array(
				'title'  => $item['title'] ?? '',
				'tag'    => $item['tag'] ?? '',
				'image'  => ! empty( $item['image']['url'] ) ? $item['image']['url'] : alhidayah_asset( 'images/collections/oud.jpg' ),
				'alt'    => $item['title'] ?? '',
				'filter' => sanitize_title( $item['filter'] ?? 'all' ),
			);
		}
		return $this->clean(
			array(
				'eyebrow' => $this->val( $s, 'eyebrow' ),
				'heading' => $this->val( $s, 'heading' ),
				'items'   => $items ? $items : null,
			)
		);
	}
}
