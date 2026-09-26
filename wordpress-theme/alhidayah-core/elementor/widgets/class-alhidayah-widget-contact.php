<?php
/**
 * Contact widget: channels, inquiry form (saved to Forms) and FAQ.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Contact.
 */
class AlHidayah_Widget_Contact extends AlHidayah_Widget_Base {

	/** @inheritDoc */
	public function get_name() {
		return 'alhidayah-contact';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'Al-Hidayah Contact Form', 'alhidayah-core' );
	}

	/** @inheritDoc */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/** @inheritDoc */
	protected function section() {
		return 'contact';
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->content_section();
		$this->text( 'pill', __( 'Pill', 'alhidayah-core' ), __( 'Contact us', 'alhidayah-core' ) );
		$this->text( 'heading', __( 'Heading', 'alhidayah-core' ), __( 'We’d love to<br>hear from you', 'alhidayah-core' ), 'textarea' );
		$this->text( 'text', __( 'Intro', 'alhidayah-core' ), __( 'Questions about an order, help choosing a signature scent, or gifting for someone special. Our team replies within one business day.', 'alhidayah-core' ), 'textarea' );
		$this->text( 'form_heading', __( 'Form heading', 'alhidayah-core' ), __( 'Send us a message', 'alhidayah-core' ) );
		$this->add_control(
			'note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Messages are saved under Forms in the dashboard and emailed to you. Contact details come from Appearance → Customize → Al-Hidayah → Contact details.', 'alhidayah-core' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'faq_section',
			array(
				'label' => __( 'FAQ', 'alhidayah-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->toggle( 'show_faq', __( 'Show FAQ', 'alhidayah-core' ) );
		$this->text( 'faq_heading', __( 'FAQ heading', 'alhidayah-core' ), __( 'Frequently asked<br>questions', 'alhidayah-core' ), 'textarea' );
		$repeater = new Repeater();
		$repeater->add_control(
			'question',
			array(
				'label' => __( 'Question', 'alhidayah-core' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'answer',
			array(
				'label' => __( 'Answer', 'alhidayah-core' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);
		$this->add_control(
			'faq',
			array(
				'label'       => __( 'Questions', 'alhidayah-core' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ question }}}',
				'description' => __( 'Leave empty to use the standard questions.', 'alhidayah-core' ),
			)
		);
		$this->end_controls_section();
		$this->style_controls();
	}

	/** @inheritDoc */
	protected function style_targets() {
		return array(
			'heading' => array( __( 'Heading', 'alhidayah-core' ), '.contact-intro h1' ),
		);
	}

	/** @inheritDoc */
	protected function args( $s ) {
		$faq = array();
		foreach ( (array) ( $s['faq'] ?? array() ) as $row ) {
			if ( ! empty( $row['question'] ) ) {
				$faq[] = array( $row['question'], $row['answer'] ?? '' );
			}
		}
		$args             = $this->clean(
			array(
				'pill'         => $this->val( $s, 'pill' ),
				'heading'      => $this->val( $s, 'heading' ),
				'text'         => $this->val( $s, 'text' ),
				'form_heading' => $this->val( $s, 'form_heading' ),
				'faq_heading'  => $this->val( $s, 'faq_heading' ),
				'faq'          => $faq ? $faq : null,
			)
		);
		$args['show_faq'] = 'yes' === $this->val( $s, 'show_faq' );
		return $args;
	}
}
