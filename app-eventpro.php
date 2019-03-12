<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://roadmapstudios.com/
 * @since             1.0.0
 * @package           App_Event
 * @copyright         Copyright © 2019 Roadmap Studios
 *
 * @wordpress-plugin
 * Plugin Name:       App Event
 * Plugin URI:        https://eventpro.ky/
 * Description:       This is plugin for connecting EventPro.
 * Version:           1.0.0
 * Author:            Roadmap Studios
 * Author URI:        https://roadmapstudios.com/
 * License:           GNU General Public License v3.0
 * Domain Path:       /languages
 * Text Domain:       AppEvent
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
if ( ! defined( 'App_EVENT_VERSION' ) ) {
	define( 'APP_EVENT_VERSION', '1.0.0' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-app-event-activator.php
 */
function activate_app_event() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-app-event-activator.php';
	App_Event_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-app-event-deactivator.php
 */
function deactivate_app_event() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-app-event-deactivator.php';
	App_Event_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_app_event' );
register_deactivation_hook( __FILE__, 'deactivate_app_event' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-app-event.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_app_event() {
	$plugin = new App_Event();
	$plugin->run();
}
run_app_event();
