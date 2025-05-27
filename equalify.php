<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://easya11yguide.com
 * @since             1.0.0
 * @package           Equalify
 *
 * @wordpress-plugin
 * Plugin Name:       Equalify
 * Plugin URI:        https://easya11yguide.com/equalify
 * Description:       Equalify client for WordPress. Manage your Equalify properties, scans, reports, history, and users within WordPress.
 * Requires at least: 5.7
 * Requires PHP:      8.1
 * Version:           1.0.0
 * Author:            Easy A11y Guide
 * Author URI:        https://easya11yguide.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       equalify
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EQUALIFY_VERSION', '1.0.0' );
define( 'EQUALIFY_FILE', __FILE__ );
define( 'EQUALIFY_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-equalify-activator.php
 */
function activate_equalify() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-equalify-activator.php';
	Equalify_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-equalify-deactivator.php
 */
function deactivate_equalify() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-equalify-deactivator.php';
	Equalify_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_equalify' );
register_deactivation_hook( __FILE__, 'deactivate_equalify' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-equalify.php';

/**
 * Add settings to main plugins screen
 */
function equalify_action_links($actions) {
    // Add your custom action links here
    $custom_links = array(
        'settings' => '<a href="' . admin_url('options-general.php?page=equalify-settings') . '">' . __('Settings', EQUALIFY_FILE) . '</a>',
    );
    return array_merge($actions, $custom_links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'equalify_action_links');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_equalify() {

	$plugin = new Equalify();
	$plugin->run();

}
run_equalify();
