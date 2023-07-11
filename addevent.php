<?php
/**
 * Plugin Name: AddEvent Shortcodes
 * Plugin URI: https://github.com/user/plugin
 * Description: Provides two shortcodes, 'addevent_button' and 'addevent_links', for adding events to various calendar services.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: addevent-shortcodes
 * Domain Path: /languages
 */

namespace AddEvent;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Add_Event {
	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include necessary files
	 */
	public function includes() {
		define( 'ADDEVENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		require_once ADDEVENT_PLUGIN_DIR . 'includes/class-add-event-shortcodes.php';
		require_once ADDEVENT_PLUGIN_DIR . 'includes/class-add-event-shortcode-helper.php';
		require_once ADDEVENT_PLUGIN_DIR . 'includes/class-add-event-admin-notices.php';
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'admin_notices', array( Add_Event_Admin_Notices::class, 'display_admin_notices' ) );
	}

	/**
	 * Enqueue scripts
	 */
	public static function enqueue_scripts() {
		wp_enqueue_script( 'addevent', 'https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js', array(), null, true );
	}

	/**
	 * Register shortcodes
	 */
	public function register_shortcodes() {
		$shortcodes = new Add_Event_Shortcodes();
		add_shortcode( 'addevent_button', array( $shortcodes, 'addevent_button_shortcode' ) );
		add_shortcode( 'addevent_links', array( $shortcodes, 'addevent_links_shortcode' ) );
	}
}

new Add_Event();
