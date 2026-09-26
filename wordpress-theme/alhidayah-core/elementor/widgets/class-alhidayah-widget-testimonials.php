<?php
/**
 * Testimonials marquee widget (reads the Testimonials menu).
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;

/**
 * Testimonials.
 */
class AlHidayah_Widget_Testimonials extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-testimonials';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Testimonials', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	/** @inheritDoc */
	protected function section() {
		return 'testimonials';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->content_section();
		$this->text( 'eyebrow', __( 'Pill', 'alhidayah-core' ), __( 'Testimonials', 'alhidayah-core' ) );
		$this->text( 'heading', __( 'Heading', 'alhidayah-core' ), __( 'What our customers say<br>about our products', 'alhidayah-core' ), 'textarea' );
		$this->add_control(
			'limit',
			array(
				'label'       => __( 'Number of reviews', 'alhidayah-core' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 24,
				'default'     => 8,
				'description' => __( 'Reviews are managed under Testimonials in the dashboard.', 'alhidayah-core' ),
			)
		);
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'heading' => array( __( 'Heading', 'alhidayah-core' ), '.section-heading h2' ),
			'quote'   => array( __( 'Quote', 'alhidayah-core' ), '.testimonial blockquote p' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		$args          = $this->clean(
			array(
				'eyebrow' => $this->val( $s, 'eyebrow' ),
				'heading' => $this->val( $s, 'heading' ),
			)
		);
		$args['items'] = alhidayah_testimonials( max( 1, (int) $this->val( $s, 'limit' ) ) );
		return $args;
	}
}
