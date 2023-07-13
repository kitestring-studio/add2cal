<?php

namespace AddEvent;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Add_Event {
	/**
	 * Class constructor
	 */
	public function __construct() {
//		$this->includes();
		$this->init_hooks();
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
		add_shortcode( 'addevent_button', array( $shortcodes, 'addevent_links_shortcode' ) );
		add_shortcode( 'addevent_links', array( $shortcodes, 'addevent_links_shortcode' ) );
	}
}

