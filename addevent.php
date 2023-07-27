<?php
/**
 * Plugin Name: Add2Cal Shortcodes
 * Plugin URI: https://github.com/kitestring-studio/add2cal
 * Description: Provides two shortcodes, 'addevent_button' and 'addevent_links', for adding events to various calendar services.
 * Version: 1.0.0
 * Author: Kitestring Studio
 * Author URI: https://kitestring.studio
 * Text Domain: addevent-shortcodes
 * Domain Path: /languages
 */

namespace AddEvent;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once __DIR__ . '/vendor/autoload.php';

new Add_Event();
