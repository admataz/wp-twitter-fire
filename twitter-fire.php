<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Twitter Fire
 * @author    Adam Davis <adam@admataz.com>
 * @license   GPL-2.0+
 * @link      http://admataz.com
 * @copyright 2021 Adam Davis
 *
 * @wordpress-plugin
 * Plugin Name:  twitter-fire
 * Plugin URI:        http://admataz.com
 * Description:       A simple way to embed a twitter timeline - provides all the caching locally and authentication with Twitter
 * Version:           2.0.0
 * Author: Adam Davis
 * Author URI:        http://admataz.com
 * Text Domain:       twitter-fire-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */ 

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . '/public/class-twitter-fire.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/Twitterfier.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
// register_activation_hook( __FILE__, array( 'Twitter_Fire', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'Twitter_Fire', 'deactivate' ) );

// register_activation_hook( __FILE__, array('Twitter_Fire','setup_cron') );
// register_deactivation_hook( __FILE__, array('Twitter_Fire','remove_cron') );


/*
 * TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 */
// add_action( 'plugins_loaded', array( 'Twitter_Fire', 'get_instance' ) );

$t = new Twitter_Fire();
// add_action('do_get_tweets', array($t,'get_tweets'));
/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * TODO:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

  require_once( plugin_dir_path( __FILE__ ) . '/admin/class-twitter-fire-admin.php' );
  add_action( 'plugins_loaded', array( 'Twitter_Fire_Admin', 'get_instance' ) );

}
