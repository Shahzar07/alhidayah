<?php
/**
 * Ingredients widget.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ingredients.
 */
class AlHidayah_Widget_Ingredients extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-ingredients';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Ingredients', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-flip-box';
	}

	/** @inheritDoc */
	protected function section() {
		return 'ingredients';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->content_section();
		$this->text( 'pill', __( 'Pill', 'alhidayah-core' ), __( 'Soulful creations. Beautiful ingredients.', 'alhidayah-core' ) );
		$this->text( 'heading', __( 'Heading', 'alhidayah-core' ), __( 'Only high-quality<br>perfume ingredients', 'alhidayah-core' ), 'textarea' );
		$this->text( 'text', __( 'Text', 'alhidayah-core' ), __( 'We create perfumes that can be enjoyed to the fullest,<br class="desktop-br"> using ingredients whose quality is beyond doubt.', 'alhidayah-core' ), 'textarea' );
		$this->text( 'button_label', __( 'Button', 'alhidayah-core' ), __( 'View our story', 'alhidayah-core' ) );
		$this->url( 'button_url', __( 'Button link (empty = Our Story page)', 'alhidayah-core' ) );
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'heading' => array( __( 'Heading', 'alhidayah-core' ), '.ingredients h2' ),
			'text'    => array( __( 'Text', 'alhidayah-core' ), '.ingredients p' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		return $this->clean(
			array(
				'pill'         => $this->val( $s, 'pill' ),
				'heading'      => $this->val( $s, 'heading' ),
				'text'         => $this->val( $s, 'text' ),
				'button_label' => $this->val( $s, 'button_label' ),
				'button_url'   => $this->link( $s, 'button_url' ),
			)
		);
	}
}
