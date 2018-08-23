<?php
/**
 * Plugin Name: Thirdwatch
 * Plugin URI: https://www.thirdwatch.ai/
 * Description: Prevent Fraud Realtime.
 * Version: 1.0.0
 * Author: Thirdwatch
 * Author URI: https://www.thirdwatch.ai/
 * Text Domain: thirdwatch
 * Domain Path: /i18n/languages/
 *
 * @package Thirdwatch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'TW_PLUGIN_FILE' ) ) {
	define( 'TW_PLUGIN_FILE', __FILE__ );
}

// Include the main WooCommerce class.
if ( ! class_exists( 'Thirdwatch' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-thirdwatch.php';
}

/**
 * Main instance of Thirdwatch.
 *
 * Returns the main instance of TW to prevent the need to use globals.
 *
 * @since  2.1
 * @return Thirdwatch
 */
function tw() {
	return Thirdwatch::instance();
}

$GLOBALS['thirdwatch'] = tw();