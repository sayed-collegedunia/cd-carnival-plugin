<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sayedakhtar.github.io
 * @since             1.0.0
 * @package           Cd_Carnival
 *
 * @wordpress-plugin
 * Plugin Name:       CD Carnival
 * Plugin URI:        https://www.collegeduniacarnival.com
 * Description:       A plugin to generate PDF tickets on lead creation and maintaining a database on number of user attended.
 * Version:           1.0.0
 * Author:            Sayed Akhtar
 * Author URI:        https://sayedakhtar.github.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cd-carnival
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
define( 'CD_CARNIVAL_VERSION', '1.0.0' );
define( 'CD_CARNIVAL_DB_TABLE', 'attendance' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cd-carnival-activator.php
 */
function activate_cd_carnival() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cd-carnival-activator.php';
	Cd_Carnival_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cd-carnival-deactivator.php
 */
function deactivate_cd_carnival() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cd-carnival-deactivator.php';
	Cd_Carnival_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cd_carnival' );
register_deactivation_hook( __FILE__, 'deactivate_cd_carnival' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) .'vendor/autoload.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-cd-carnival.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cd_carnival() {

	$plugin = new Cd_Carnival();
	$plugin->run();

}
run_cd_carnival();
