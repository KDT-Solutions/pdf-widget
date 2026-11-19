<?php
/**
 * Removes the plugin settings when the plugin is deleted.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'pdf_widget__option_name' );
