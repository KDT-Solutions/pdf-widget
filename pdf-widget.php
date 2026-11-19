<?php
/**
 * Plugin Name:       PDF Widget
 * Plugin URI:        https://wordpress.org/plugins/pdf-widget/
 * Description:       Elementor widget and [adobepdf] shortcode to embed PDF files with the Adobe PDF Embed API.
 * Version:           1.4
 * Author:            KDT-Solutions GmbH
 * Author URI:        https://kdt-solutions.ch
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:       pdf-widget
 * Requires at least: 5.0
 * Tested up to:      7.1
 * Requires PHP:      7.0
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

define( 'PDF_WIDGET_VERSION', '1.4' );
define( 'PDF_WIDGET_FILE', __FILE__ );
define( 'PDF_WIDGET_OPTION', 'pdf_widget__option_name' );

/**
 * URL of the hosted Adobe PDF Embed API (View SDK).
 * See https://developer.adobe.com/document-services/docs/overview/pdf-embed-api/howtos/
 */
define( 'PDF_WIDGET_VIEWER_URL', 'https://acrobatservices.adobe.com/view-sdk/viewer.js' );

/**
 * Embed modes supported by the Adobe PDF Embed API.
 */
function pdf_widget_embed_modes() {
	return array(
		'IN_LINE'         => __( 'In-Line', 'pdf-widget' ),
		'SIZED_CONTAINER' => __( 'Sized Container', 'pdf-widget' ),
		'FULL_WINDOW'     => __( 'Full Window', 'pdf-widget' ),
		'LIGHT_BOX'       => __( 'Lightbox', 'pdf-widget' ),
	);
}

/**
 * Returns the Adobe client ID (API key) stored in the settings.
 */
function pdf_widget_get_client_id() {
	$options = get_option( PDF_WIDGET_OPTION );
	return ( is_array( $options ) && ! empty( $options['api_key_0'] ) ) ? $options['api_key_0'] : '';
}

/**
 * Register the Adobe viewer script. It is only enqueued on pages that contain a PDF.
 */
function pdf_widget_register_assets() {
	wp_register_script( 'adobeviewer', PDF_WIDGET_VIEWER_URL, array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'pdf_widget_register_assets' );

/**
 * Builds the markup for one embedded PDF.
 *
 * @param string $url  URL of the PDF file.
 * @param array  $args embed_mode, show_download, show_print, height, id.
 * @return string
 */
function pdf_widget_render_viewer( $url, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'embed_mode'    => 'IN_LINE',
			'show_download' => true,
			'show_print'    => true,
			'height'        => '',
			'id'            => '',
		)
	);

	$url = esc_url_raw( trim( (string) $url ) );
	if ( '' === $url || '#' === $url ) {
		return '';
	}

	$modes      = pdf_widget_embed_modes();
	$embed_mode = strtoupper( (string) $args['embed_mode'] );
	if ( ! isset( $modes[ $embed_mode ] ) ) {
		$embed_mode = 'IN_LINE';
	}

	$div_id = 'adobe-dc-view-' . ( '' !== $args['id'] ? sanitize_html_class( $args['id'] ) : wp_unique_id() );

	$path      = wp_parse_url( $url, PHP_URL_PATH );
	$file_name = $path ? rawurldecode( wp_basename( $path ) ) : 'document.pdf';

	$config = array( 'embedMode' => $embed_mode );
	if ( 'LIGHT_BOX' !== $embed_mode ) {
		$config['showDownloadPDF'] = (bool) $args['show_download'];
		$config['showPrintPDF']    = (bool) $args['show_print'];
	}

	$view = array(
		'clientId' => pdf_widget_get_client_id(),
		'divId'    => $div_id,
	);
	$file = array(
		'content'  => array( 'location' => array( 'url' => $url ) ),
		'metaData' => array( 'fileName' => $file_name ),
	);

	// In-Line grows with the document, the other modes need a fixed height.
	$style = '';
	if ( 'IN_LINE' !== $embed_mode && 'LIGHT_BOX' !== $embed_mode ) {
		$height = trim( (string) $args['height'] );
		if ( '' === $height ) {
			$height = '600px';
		} elseif ( is_numeric( $height ) ) {
			$height .= 'px';
		}
		$style = ' style="height:' . esc_attr( $height ) . ';"';
	}

	wp_enqueue_script( 'adobeviewer' );

	$vars = 'var v=' . wp_json_encode( $view ) . ',f=' . wp_json_encode( $file ) . ',c=' . wp_json_encode( $config ) . ';';

	if ( 'LIGHT_BOX' === $embed_mode ) {
		// Lightbox: show a link, open the viewer as overlay on click (falls back to the plain PDF link).
		$js = '(function(){' . $vars . 'var a=document.getElementById(v.divId+"-link");if(!a){return;}'
			. 'a.addEventListener("click",function(e){if(!(window.AdobeDC&&window.AdobeDC.View)){return;}e.preventDefault();new AdobeDC.View(v).previewFile(f,c);});})();';

		return '<div class="pdf-widget"><div id="' . esc_attr( $div_id ) . '" class="adobe-dc-view"></div>'
			. '<a href="' . esc_url( $url ) . '" id="' . esc_attr( $div_id ) . '-link" class="pdf-widget-lightbox-link">' . esc_html( $file_name ) . '</a>'
			. '<script>' . $js . '</script></div>';
	}

	$js = '(function(){' . $vars
		. 'function r(){if(!document.getElementById(v.divId)){return;}new AdobeDC.View(v).previewFile(f,c);}'
		. 'if(window.AdobeDC&&window.AdobeDC.View){r();}else{document.addEventListener("adobe_dc_view_sdk.ready",r);}})();';

	return '<div class="pdf-widget"><div id="' . esc_attr( $div_id ) . '" class="adobe-dc-view"' . $style . '></div>'
		. '<script>' . $js . '</script></div>';
}

/**
 * Shortcode [adobepdf link="..." embedmode="IN_LINE" download="yes" print="yes" height="600"].
 */
function adobe_pdf_embed( $atts ) {
	$a = shortcode_atts(
		array(
			'link'      => '',
			'embedmode' => 'IN_LINE',
			'download'  => 'yes',
			'print'     => 'yes',
			'height'    => '',
		),
		$atts,
		'adobepdf'
	);

	return pdf_widget_render_viewer(
		$a['link'],
		array(
			'embed_mode'    => $a['embedmode'],
			'show_download' => ! in_array( strtolower( $a['download'] ), array( 'no', 'false', '0' ), true ),
			'show_print'    => ! in_array( strtolower( $a['print'] ), array( 'no', 'false', '0' ), true ),
			'height'        => $a['height'],
		)
	);
}
add_shortcode( 'adobepdf', 'adobe_pdf_embed' );

/**
 * Elementor integration.
 */
final class Pdf_Widget {

	const VERSION                   = PDF_WIDGET_VERSION;
	const MINIMUM_ELEMENTOR_VERSION = '3.8.0';
	const MINIMUM_PHP_VERSION       = '7.0';

	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	protected function __construct() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}
		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		// The editor preview renders widgets via AJAX, so the viewer must be loaded up front there.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'preview_scripts' ) );
	}

	public function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/pdf-widget/pdf-widget.php';
		$widgets_manager->register( new \Elementor\Pdf_Widget() );
	}

	public function preview_scripts() {
		pdf_widget_register_assets();
		wp_enqueue_script( 'adobeviewer' );
	}

	private function notice( $message ) {
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
		}
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses( $message, array( 'strong' => array() ) ) );
	}

	public function admin_notice_missing_main_plugin() {
		$this->notice(
			sprintf(
				/* translators: 1: Plugin name 2: Elementor */
				esc_html__( '"%1$s" requires "%2$s" to be installed and activated for the widget. The [adobepdf] shortcode works without it.', 'pdf-widget' ),
				'<strong>' . esc_html__( 'PDF Widget', 'pdf-widget' ) . '</strong>',
				'<strong>Elementor</strong>'
			)
		);
	}

	public function admin_notice_minimum_elementor_version() {
		$this->notice(
			sprintf(
				/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pdf-widget' ),
				'<strong>' . esc_html__( 'PDF Widget', 'pdf-widget' ) . '</strong>',
				'<strong>Elementor</strong>',
				esc_html( self::MINIMUM_ELEMENTOR_VERSION )
			)
		);
	}

	public function admin_notice_minimum_php_version() {
		$this->notice(
			sprintf(
				/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pdf-widget' ),
				'<strong>' . esc_html__( 'PDF Widget', 'pdf-widget' ) . '</strong>',
				'<strong>PHP</strong>',
				esc_html( self::MINIMUM_PHP_VERSION )
			)
		);
	}
}

add_action( 'plugins_loaded', 'pdf_widget_elementor_init' );
function pdf_widget_elementor_init() {
	Pdf_Widget::get_instance();
}

/**
 * Settings page (Settings -> PDF Widget).
 */
class PDFWidget {
	private $pdf_widget__options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'pdf_widget__add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'pdf_widget__page_init' ) );
	}

	public function pdf_widget__add_plugin_page() {
		add_options_page(
			'PDF Widget',
			'PDF Widget',
			'manage_options',
			'pdf-widget',
			array( $this, 'pdf_widget__create_admin_page' )
		);
	}

	public function pdf_widget__create_admin_page() {
		$this->pdf_widget__options = get_option( PDF_WIDGET_OPTION ); ?>
		<div class="wrap">
			<h1>PDF Widget <small>by KDT-Solutions GmbH</small></h1>
			<p>
				<?php esc_html_e( 'Enter the client ID (API key) of the Adobe PDF Embed API. The domain of this site must be registered for the key.', 'pdf-widget' ); ?><br>
				<a href="https://acrobatservices.adobe.com/dc-integration-creation-app-cdn/main.html?api=pdf-embed-api" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get a free client ID from Adobe', 'pdf-widget' ); ?></a>
			</p>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'pdf_widget__option_group' );
				do_settings_sections( 'pdf-widget-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function pdf_widget__page_init() {
		register_setting(
			'pdf_widget__option_group',
			PDF_WIDGET_OPTION,
			array( $this, 'pdf_widget__sanitize' )
		);

		add_settings_section(
			'pdf_widget__setting_section',
			__( 'Settings', 'pdf-widget' ),
			'__return_false',
			'pdf-widget-admin'
		);

		add_settings_field(
			'api_key_0',
			__( 'Client ID (API Key)', 'pdf-widget' ),
			array( $this, 'api_key_0_callback' ),
			'pdf-widget-admin',
			'pdf_widget__setting_section'
		);
	}

	public function pdf_widget__sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['api_key_0'] ) ) {
			$sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
		}
		return $sanitary_values;
	}

	public function api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="%1$s[api_key_0]" id="api_key_0" value="%2$s">',
			esc_attr( PDF_WIDGET_OPTION ),
			isset( $this->pdf_widget__options['api_key_0'] ) ? esc_attr( $this->pdf_widget__options['api_key_0'] ) : ''
		);
	}
}
if ( is_admin() ) {
	$pdf_widget_ = new PDFWidget();
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pw_settings_link' );
function pw_settings_link( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=pdf-widget' ) ) . '">' . esc_html__( 'Settings', 'pdf-widget' ) . '</a>';
	return $links;
}
