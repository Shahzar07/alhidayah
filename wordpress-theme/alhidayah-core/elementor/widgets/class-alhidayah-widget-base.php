<?php
/**
 * Shared base for Al-Hidayah Elementor widgets.
 *
 * Every widget renders the same template part as the theme, so pages built in Elementor
 * look identical to the default design while all copy, images and links are editable.
 *
 * @package AlHidayah_Core
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Base widget.
 */
abstract class AlHidayah_Widget_Base extends Widget_Base {

	/**
	 * Section template (template-parts/sections/{slug}.php).
	 *
	 * @return string
	 */
	abstract protected function section();

	/**
	 * Map widget settings to template arguments.
	 *
	 * @param array $s Settings.
	 * @return array
	 */
	abstract protected function args( $s );

	/**
	 * Style targets: key => [ label, selector ].
	 *
	 * @return array
	 */
	protected function style_targets() {
		return array();
	}

	/**
	 * Category.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'alhidayah' );
	}

	/**
	 * Keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'alhidayah', 'al-hidayah', 'perfume', $this->section() );
	}

	/**
	 * Theme styles and scripts are global; nothing extra to load.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'alhidayah' );
	}

	/**
	 * Render the section.
	 */
	protected function render() {
		alhidayah_section( $this->section(), $this->args( $this->get_settings_for_display() ) );
	}

	/* -------------------------------------------------------- control helpers */

	/**
	 * Open the content section.
	 *
	 * @param string $label Label.
	 */
	protected function content_section( $label = '' ) {
		$this->start_controls_section(
			'content',
			array(
				'label' => $label ? $label : __( 'Content', 'alhidayah-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
	}

	/**
	 * Text control.
	 *
	 * @param string $id      ID.
	 * @param string $label   Label.
	 * @param string $default Default.
	 * @param string $type    text|textarea.
	 */
	protected function text( $id, $label, $default = '', $type = 'text' ) {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => 'textarea' === $type ? Controls_Manager::TEXTAREA : Controls_Manager::TEXT,
				'default'     => $default,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => 'textarea' === $type ? __( 'Use &lt;br&gt; for a line break and &lt;span&gt;…&lt;/span&gt; to highlight a word.', 'alhidayah-core' ) : '',
			)
		);
	}

	/**
	 * URL control.
	 *
	 * @param string $id      ID.
	 * @param string $label   Label.
	 * @param string $default Default URL.
	 */
	protected function url( $id, $label, $default = '' ) {
		$this->add_control(
			$id,
			array(
				'label'   => $label,
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => $default ),
				'dynamic' => array( 'active' => true ),
			)
		);
	}

	/**
	 * Switcher control.
	 *
	 * @param string $id      ID.
	 * @param string $label   Label.
	 * @param bool   $default Default on.
	 */
	protected function toggle( $id, $label, $default = true ) {
		$this->add_control(
			$id,
			array(
				'label'        => $label,
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $default ? 'yes' : '',
			)
		);
	}

	/**
	 * Media control.
	 *
	 * @param string $id    ID.
	 * @param string $label Label.
	 * @param string $help  Description.
	 */
	protected function media( $id, $label, $help = '' ) {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => '' ),
				'description' => $help,
				'dynamic'     => array( 'active' => true ),
			)
		);
	}

	/**
	 * Register colour + typography controls for the style targets.
	 */
	protected function style_controls() {
		$targets = $this->style_targets();
		if ( ! $targets ) {
			return;
		}
		$this->start_controls_section(
			'style',
			array(
				'label' => __( 'Style', 'alhidayah-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		foreach ( $targets as $key => $target ) {
			$this->add_control(
				$key . '_heading',
				array(
					'label'     => $target[0],
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
			$this->add_control(
				$key . '_color',
				array(
					'label'     => __( 'Colour', 'alhidayah-core' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} ' . $target[1] => 'color: {{VALUE}};' ),
				)
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => $key . '_type',
					'selector' => '{{WRAPPER}} ' . $target[1],
				)
			);
		}
		$this->end_controls_section();
	}

	/**
	 * Text setting or default.
	 *
	 * @param array  $s   Settings.
	 * @param string $key Key.
	 * @return string
	 */
	protected function val( $s, $key ) {
		return isset( $s[ $key ] ) ? (string) $s[ $key ] : '';
	}

	/**
	 * URL setting.
	 *
	 * @param array  $s   Settings.
	 * @param string $key Key.
	 * @return string
	 */
	protected function link( $s, $key ) {
		return isset( $s[ $key ]['url'] ) ? (string) $s[ $key ]['url'] : '';
	}

	/**
	 * Media URL setting.
	 *
	 * @param array  $s   Settings.
	 * @param string $key Key.
	 * @return string
	 */
	protected function image( $s, $key ) {
		return ! empty( $s[ $key ]['url'] ) ? (string) $s[ $key ]['url'] : '';
	}

	/**
	 * Drop empty values so the template falls back to its defaults.
	 *
	 * @param array $args Args.
	 * @return array
	 */
	protected function clean( $args ) {
		return array_filter(
			$args,
			static function ( $v ) {
				return '' !== $v && null !== $v;
			}
		);
	}
}
