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

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'includes/class-addeventbase.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-addeventbutton.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-addeventlinks.php';

// Define the addevent_button shortcode
add_shortcode('addevent_button', array('AddEventButton', 'render_shortcode'));

// Define the addevent_links shortcode
add_shortcode('addevent_links', array('AddEventLinks', 'render_shortcode'));

// Enqueue the Addevent JavaScript file when required
function enqueue_addevent_script() {
	if (!wp_script_is('addevent', 'enqueued')) {
		wp_enqueue_script('addevent', 'https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js', array(), null, true);
	}
}
add_action('wp_enqueue_scripts', 'enqueue_addevent_script');

// Show admin notice if the API key is not defined
function addevent_admin_notice() {
	if (!defined('ADDEVENT_API_KEY') && current_user_can('activate_plugins')) {
		?>
		<div class="notice notice-warning">
			<p><?php _e('The AddEvent plugin needs the client ID (API key) defined in wp-config.php.', 'addevent-shortcodes'); ?></p>
		</div>
		<?php
	}
}
add_action('admin_notices', 'addevent_admin_notice');

register_activation_hook(__FILE__, function() {
	if (!defined('ADDEVENT_API_KEY')) {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die(__('AddEvent requires the client ID to be defined in wp-config.php', 'addevent'));
	}
});

register_deactivation_hook(__FILE__, function() {
	// Clean up, e.g. delete plugin options...
});
