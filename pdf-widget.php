<?php
/**
 * PDF Widget
 *
 * Plugin Name: PDF Widget
 * Plugin URI:  https://github.com/KDT-Solutions/pdf-widget
 * Description: Enables a Elementor Widget and [adobepdf] Shortcode to embed PDF's withe Adobe PDF Embed API.
 * Version:     1.1
 * Author:      KDT-Solutions GmbH
 * Author URI:  https://kdt-solutions.ch
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Tested up to: 6.1.1
 * Requires PHP: 5.2.4
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Pdf_Widget {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.5.11';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '6.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * The single instance of the class.
	 */
	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */

	protected function __construct() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}
		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		require_once('widgets/pdf-widget/pdf-widget.php');


		// Register Widget
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

		// Register Widget Styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );

		// Register Widget Scripts
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );

	}


	public function register_widgets() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Pdf_Widget() );

	}

	public function widget_styles() {
		wp_enqueue_style( 'pdf-widget-css', plugins_url( 'widgets/pdf-widget/css/pdf-widget.css', __FILE__ ) );

	}

	public function widget_scripts() {
		wp_enqueue_script( 'pdf-widget-js', plugins_url( 'widgets/pdf-widget/js/pdf-widget.js', __FILE__ ), array( 'jquery' ) );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'pdf-widget' ),
			'<strong>' . esc_html__( 'PDF Widget', 'pdf-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pdf-widget' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pdf-widget' ),
			'<strong>' . esc_html__( 'PDF Widget', 'pdf-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pdf-widget' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
		/* 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pdf-widget' ),
			'<strong>' . esc_html__( 'PDF Widget', 'pdf-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'pdf-widget' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

}

add_action( 'init', 'Pdf_Widget_elementor_init' );
function Pdf_Widget_elementor_init() {
	Pdf_Widget::get_instance();
}

function adobe_pdf_embed($atts) {
    $default = array(
        'link' => '#',
    );
    $a = shortcode_atts($default, $atts);
    return  '<div id="adobe-dc-view"></div><script src="https://documentcloud.adobe.com/view-sdk/viewer.js"></script> <script type="text/javascript">document.addEventListener("adobe_dc_view_sdk.ready", function(){ 		var adobeDCView = new AdobeDC.View({clientId: "6c9dd0d79a2b448f9055ecfb41bcc7ed", divId: "adobe-dc-view"}); 		adobeDCView.previewFile({ 			content:{location: {url: "'.$a['link'].'"}}, 			metaData:{fileName: "Artikel.pdf"} 		}, {embedMode: "IN_LINE"}); 	}); </script>';

}
add_shortcode('adobepdf', 'adobe_pdf_embed');

class PDFWidget {
	private $pdf_widget__options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'pdf_widget__add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'pdf_widget__page_init' ) );
	}

	public function pdf_widget__add_plugin_page() {
		add_options_page(
			'PDF Widget ', // page_title
			'PDF Widget ', // menu_title
			'manage_options', // capability
			'pdf-widget', // menu_slug
			array( $this, 'pdf_widget__create_admin_page' ) // function
		);
	}

	public function pdf_widget__create_admin_page() {
		$this->pdf_widget__options = get_option( 'pdf_widget__option_name' ); ?>

		<div class="wrap">
			<h2>PDF Widget <small>by KDT-Solutions GmbH</small></h2>
			<p>Add the Adobe PDF Embed API Key to have this Plugin working: <br>
<a href='https://documentcloud.adobe.com/dc-integration-creation-app-cdn/main.html?api=pdf-embed-api' target='_blank'>Adobe Developer</a></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'pdf_widget__option_group' );
					do_settings_sections( 'pdf-widget-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function pdf_widget__page_init() {
		register_setting(
			'pdf_widget__option_group', // option_group
			'pdf_widget__option_name', // option_name
			array( $this, 'pdf_widget__sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'pdf_widget__setting_section', // id
			'Settings', // title
			array( $this, 'pdf_widget__section_info' ), // callback
			'pdf-widget-admin' // page
		);

		add_settings_field(
			'api_key_0', // id
			'API Key', // title
			array( $this, 'api_key_0_callback' ), // callback
			'pdf-widget-admin', // page
			'pdf_widget__setting_section' // section
		);
	}

	public function pdf_widget__sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['api_key_0'] ) ) {
			$sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
		}

		return $sanitary_values;
	}

	public function pdf_widget__section_info() {

	}

	public function api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="pdf_widget__option_name[api_key_0]" id="api_key_0" value="%s">',
			isset( $this->pdf_widget__options['api_key_0'] ) ? esc_attr( $this->pdf_widget__options['api_key_0']) : ''
		);
	}

}
if ( is_admin() )
	$pdf_widget_ = new PDFWidget();

/*
 * Retrieve this value with:
 * $pdf_widget__options = get_option( 'pdf_widget__option_name' ); // Array of All Options
 * $api_key_0 = $pdf_widget__options['api_key_0']; // API Key
 */

 add_filter( 'plugin_action_links_pdf-widget/pdf-widget.php', 'pw_settings_link' );
function pw_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'pdf-widget',
		get_admin_url() . 'admin.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}//end pw_settings_link()
