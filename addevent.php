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

require_once 'vendor/autoload.php';


new Add_Event();
