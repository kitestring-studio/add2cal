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
		$parameters['start'] = self::format_date($parameters['start'], $parameters['24h']);
		$parameters['end'] = self::calculate_end_date($parameters['start'], $parameters['end'], $parameters['length'], $parameters['24h']);

		// Prepare the data for the URL.
		$url_data = array(
			'client' => ADDEVENT_API_KEY,
			'start' => rawurlencode($parameters['start']),
			'end' => rawurlencode($parameters['end']),
			'title' => rawurlencode($parameters['event_title']),
			'description' => rawurlencode($parameters['description']),
			'location' => rawurlencode($parameters['location']),
			'timezone' => rawurlencode($parameters['timezone']),
		);

		// Start output buffering.
		ob_start();
		foreach (self::get_services() as $service) {
			// Only include the services specified by the 'services' attribute.
			if (!in_array($service, $parameters['services'])) {
				continue;
			}

			$url_data['service'] = $service;
			$url = 'https://www.addevent.com/dir/?' . http_build_query($url_data);
			?>
            <a href="<?php echo esc_url($url); ?>"><?php echo esc_html(ucfirst($service)); ?></a>
			<?php
		}

		// End output buffering and return buffered output.
		return ob_get_clean();
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
