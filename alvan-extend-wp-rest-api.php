<?php
/**
 * Plugin Name: Alvan Extend REST API
 * Plugin URI: https://github.com/Alvan-Judi/alvan-extend-rest-api
 * Description: A plugin example on how to add more datas to the post endpoint of the REST API
 * Version: 1.0.0
 * Author: Alexis Vandepitte
 * Licence: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: alvan-extend-wp-rest-api
 * Domain Path: /languages
 * Requires at least: 5.2
 * Requires PHP: 5.6
 * Tested up to: 5.3.2
 *
 * @package AERA
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AERA\Plugin;

// Define plugin basename file.
define( 'AERA_BASENAME', plugin_basename( __FILE__ ) );
define( 'AERA_OPTION', 'aera_options' );

require_once __DIR__ . '/autoload.php';

/**
 * Init
 *
 * @since 1.0.0
 * @return Plugin Singleton instance
 */
function aera_plugin() {
	return Plugin::get_instance();
}

// Launch.
add_action( 'plugins_loaded', array( aera_plugin(), 'hooks' ) );

// Activate hook.
register_activation_hook( __FILE__, 'aera_plugin_uninstall' );
function aera_plugin_uninstall() {
	register_uninstall_hook( __FILE__, '\AERA\Plugin::plugin_uninstall' );
}
