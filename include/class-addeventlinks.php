<?php

namespace AddEvent;

class AddEventLinks extends AddEventBase {
	public static function render_shortcode($atts) {
		if (!defined('ADDEVENT_API_KEY')) {
			if (current_user_can('manage_options')) {
				add_action('admin_notices', array(self::class, 'admin_notice_missing_api_key'));
			}
			return '';
		}
		$parameters = shortcode_atts(self::get_shortcode_defaults(), $atts);
		try {
			self::validate_parameters($parameters);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		// Proceed with the links rendering...
	}

	public static function admin_notice_missing_api_key() {
		?>
		<div class="notice notice-error">
			<p><?php _e('AddEvent requires the client ID to be defined in wp-config.php', 'addevent'); ?></p>
		</div>
		<?php
	}

	// Other necessary methods like links rendering, etc...
}
