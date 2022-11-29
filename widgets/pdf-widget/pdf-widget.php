<?php
namespace Elementor;

class Pdf_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'pdf-widget';
	}

	/**
	 * Get widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return 'PDF Widget';
	}

	/**
	 * Get widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-document-file';
	}

	/**
	 * Get widget categories.
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Register widget controls.
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		/**
		 *  Here you can add your controls. The controls below are only examples.
		 *  Check this: https://developers.elementor.com/elementor-controls/
		 *
		 **/


		$this->end_controls_section();
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Datei', 'pdf-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'urltext',
			[
				'label' => __( 'URLtext', 'pdf-widget' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
			]
		);

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		/**
		 *  Here you can output your control data and build your content.
		 **/
		$pdf_widget__options = get_option( 'pdf_widget__option_name' );
		$api_key = $pdf_widget__options['api_key_0'];
 ?>
 <div id="adobe-dc-view"></div>
 <script src="https://documentcloud.adobe.com/view-sdk/viewer.js"></script>
 <script type="text/javascript">
 	document.addEventListener("adobe_dc_view_sdk.ready", function(){
 		var adobeDCView = new AdobeDC.View({clientId: "<?php echo $api_key; ?>", divId: "adobe-dc-view"});
 		adobeDCView.previewFile({
 			content:{location: {url: "<?php echo $settings['urltext']['url'] ?>"}},
 			metaData:{fileName: "Artikel.pdf"}
 		}, {embedMode: "IN_LINE"});
 	});
 </script>
 <?php

	}

	/**
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 * With JS templates we don’t really need to retrieve the data using a special function, its done by Elementor for us.
	 * The data from the controls stored in the settings variable.
	 */
	protected function _content_template() {
		?>
		<div id="adobe-dc-view"></div>
		<script src="https://documentcloud.adobe.com/view-sdk/viewer.js"></script>
		<script type="text/javascript">
			document.addEventListener("adobe_dc_view_sdk.ready", function(){
				var adobeDCView = new AdobeDC.View({clientId: "<?php echo $api_key; ?>", divId: "adobe-dc-view"});
				adobeDCView.previewFile({
					content:{location: {url: "{{{ settings.urltext.url }}}"}},
					metaData:{fileName: "Artikel.pdf"}
				}, {embedMode: "IN_LINE"});
			});
		</script>
		<?php
	}
}
