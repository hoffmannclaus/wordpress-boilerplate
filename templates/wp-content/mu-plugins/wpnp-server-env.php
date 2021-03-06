<?php
/*
Plugin Name: WP_Server_Environment in Admin Bar
Plugin URI:
Description: Shows a color coded status of the active server environment in the Wordpress Admin Bar. To use this plugin, you need to add a constant named <code>WP_SERVER_ENVIRONMENT</code> to your <code>wp-config.php</code> file, with one of the following values: <code>local</code>, <code>staging</code>, <code>live</code>
Version: 1.2.0
Author: Neonpastell GmbH
Author URI: http://www.neonpastell.de
*/

/**
* to show for all users, add filter: add_filter( 'npwp_env_admin_bar_capabilities', '__return_true');
*/


if (!defined( 'ABSPATH' )) { exit; }

if ( ! class_exists( 'NPWP_ENV_Admin_Bar' ) && defined('WP_SERVER_ENVIRONMENT') ) :

class NPWP_ENV_Admin_Bar {

	/**
	 * Handles initializing this class and returning the singleton instance after it's been cached.
	 *
	 * @return null|NPWP_ENV_Admin_Bar
	 */
	public static function get_instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
			self::init_actions();
		}

		return $instance;
	}

	/**
	 * An empty constructor
	 */
	public function __construct() { /* Purposely do nothing here */ }

	/**
	 * Handles registering hooks that initialize this plugin.
	 */
	public static function init_actions() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_admin_bar_item'), 10); // 10|15|25|100
		add_action( 'admin_head', array( __CLASS__, 'add_styles_for_admin_bar_item') ); // add styles to admin head
		add_action( 'wp_head', array( __CLASS__, 'add_styles_for_admin_bar_item') ); // add styles to frontend
	}


	/**
	 * adds new item in admin_bar_menu
	 */
	public static function add_admin_bar_item( $wp_admin_bar ) {
		$user_rights = apply_filters( 'npwp_env_admin_bar_capabilities', current_user_can( 'manage_options' ));

		if ( $user_rights && is_admin_bar_showing() ) {
			$args = array(
				'id'    => 'wpnp-env',
				'title' => '' . strtoupper(WP_SERVER_ENVIRONMENT),
				'html' => 'div',
				'href'  => get_admin_url(),
				'meta'  => array( 'title' => 'WP_SERVER_ENVIRONMENT: ' . WP_SERVER_ENVIRONMENT )
			);
			$wp_admin_bar->add_node( $args );
			do_action( 'npwp_env_admin_bar', $wp_admin_bar );
		}
	}

	/**
	 * adds styles for menu item
	 */
	public static function add_styles_for_admin_bar_item() {
		$user_rights = apply_filters( 'npwp_env_admin_bar_capabilities', current_user_can( 'manage_options' ));

		if ( $user_rights && is_admin_bar_showing() ) {
			// add color to env menu
			echo '<style type="text/css">
				#wpadminbar { box-sizing: border-box; }
				#wpadminbar #wp-admin-bar-wpnp-env > .ab-item { color: #eee; }
				#wpadminbar #wp-admin-bar-wpnp-env > .ab-item:before {
					position: relative;
					float: left;
					font: 400 20px/1 dashicons;
					speak: none;
					padding: 4px 0;
					-webkit-font-smoothing: antialiased;
					-moz-osx-font-smoothing: grayscale;
					background-image: none!important;
					margin-right: 6px;
					color: #a0a5aa;
					color: rgba(240,245,250,.6);
					position: relative;
					-webkit-transition: all .1s ease-in-out;
					transition: all .1s ease-in-out;
					content: "\f325";
					top: 2px;
				}';

			// additional styles for different environments
			$style_live    = '#wpadminbar #wp-admin-bar-wpnp-env > .ab-item { background: #C50114; !important } #wpadminbar { border-bottom: 5px solid #C50114; }';
			$style_staging = '#wpadminbar #wp-admin-bar-wpnp-env > .ab-item { background: #D18300; !important } #wpadminbar { border-bottom: 5px solid #D18300; }';
			$style_local   = '#wpadminbar #wp-admin-bar-wpnp-env > .ab-item { background: #093F11; !important }';

			switch (WP_SERVER_ENVIRONMENT) {
				case 'local':
				case 'dev':
				case 'development':
					echo $style_local;
					break;
				case 'live':
				case 'production':
					echo $style_live;
					break;
				default:
					// do nothing
					echo $style_staging;
			}

			echo '</style>';
		}
	}

}

NPWP_ENV_Admin_Bar::get_instance();

endif;
