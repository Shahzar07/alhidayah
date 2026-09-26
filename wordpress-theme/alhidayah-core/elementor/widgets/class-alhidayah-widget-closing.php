<?php
/**
 * Closing call-to-action widget.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Closing.
 */
class AlHidayah_Widget_Closing extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-closing';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Call to Action', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/** @inheritDoc */
	protected function section() {
		return 'closing';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->content_section();
		$this->text( 'eyebrow', __( 'Eyebrow', 'alhidayah-core' ), __( 'A SCENT OF YOUR OWN', 'alhidayah-core' ) );
		$this->text( 'heading', __( 'Heading', 'alhidayah-core' ), __( 'Leave a little<br>of <span>yourself</span> everywhere.', 'alhidayah-core' ), 'textarea' );
		$this->text( 'button_label', __( 'Button', 'alhidayah-core' ), __( 'Find your signature', 'alhidayah-core' ) );
		$this->url( 'button_url', __( 'Button link', 'alhidayah-core' ), '#shop' );
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'heading'   => array( __( 'Heading', 'alhidayah-core' ), '.closing h2' ),
			'highlight' => array( __( 'Highlighted word', 'alhidayah-core' ), '.closing h2 span' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		return $this->clean(
			array(
				'eyebrow'      => $this->val( $s, 'eyebrow' ),
				'heading'      => $this->val( $s, 'heading' ),
				'button_label' => $this->val( $s, 'button_label' ),
				'button_url'   => $this->link( $s, 'button_url' ),
			)
		);
	}
}
