<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Pdf_Widget extends Widget_Base {

	public function get_name() {
		return 'pdf-widget';
	}

	public function get_title() {
		return 'PDF Widget';
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return array( 'basic' );
	}

	public function get_keywords() {
		return array( 'pdf', 'adobe', 'document', 'embed' );
	}

	public function get_script_depends() {
		return array( 'adobeviewer' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Datei', 'pdf-widget' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		// Control ID "urltext" is kept for backwards compatibility with existing pages.
		$this->add_control(
			'urltext',
			array(
				'label'   => __( 'PDF URL', 'pdf-widget' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'embed_mode',
			array(
				'label'   => __( 'Embed Mode', 'pdf-widget' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'IN_LINE',
				'options' => pdf_widget_embed_modes(),
			)
		);

		$this->add_control(
			'height',
			array(
				'label'      => __( 'Height', 'pdf-widget' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 2000,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 600,
				),
				'condition'  => array(
					'embed_mode' => array( 'SIZED_CONTAINER', 'FULL_WINDOW' ),
				),
			)
		);

		$this->add_control(
			'show_download',
			array(
				'label'        => __( 'Download button', 'pdf-widget' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'embed_mode!' => 'LIGHT_BOX',
				),
			)
		);

		$this->add_control(
			'show_print',
			array(
				'label'        => __( 'Print button', 'pdf-widget' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'embed_mode!' => 'LIGHT_BOX',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$url = isset( $settings['urltext']['url'] ) ? $settings['urltext']['url'] : '';
		if ( '' === trim( (string) $url ) ) {
			if ( Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="pdf-widget-placeholder">' . esc_html__( 'Please select a PDF file.', 'pdf-widget' ) . '</div>';
			}
			return;
		}

		$height = '';
		if ( ! empty( $settings['height']['size'] ) ) {
			$height = $settings['height']['size'] . ( ! empty( $settings['height']['unit'] ) ? $settings['height']['unit'] : 'px' );
		}

		// Output is escaped in pdf_widget_render_viewer().
		echo pdf_widget_render_viewer( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$url,
			array(
				'embed_mode'    => ! empty( $settings['embed_mode'] ) ? $settings['embed_mode'] : 'IN_LINE',
				'show_download' => ! isset( $settings['show_download'] ) || 'yes' === $settings['show_download'],
				'show_print'    => ! isset( $settings['show_print'] ) || 'yes' === $settings['show_print'],
				'height'        => $height,
				'id'            => $this->get_id(),
			)
		);
	}
}
