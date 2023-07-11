<?php

namespace AddEvent;

class AddEventButton extends AddEventBase {
	public static function render_shortcode($atts) {
		$parameters = shortcode_atts(self::get_shortcode_defaults(), $atts);
		try {
			self::validate_parameters($parameters);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		// Enqueue the script.
		wp_enqueue_script('addevent', 'https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js', array(), null, true);

		// Proceed with the button rendering...
		// Prepare data for rendering.
		$parameters['start'] = self::format_date($parameters['start'], $parameters['24h']);
		$parameters['end'] = self::calculate_end_date($parameters['start'], $parameters['end'], $parameters['length'], $parameters['24h']);

		// Start output buffering.
		ob_start();
		?>
		<div title="<?php echo esc_attr($parameters['title']); ?>" class="<?php echo esc_attr($parameters['class']); ?>">
			Add to Calendar
			<span class="start"><?php echo esc_html($parameters['start']); ?></span>
			<span class="end"><?php echo esc_html($parameters['end']); ?></span>
			<span class="timezone"><?php echo esc_html($parameters['timezone']); ?></span>
			<span class="title"><?php echo esc_html($parameters['event_title']); ?></span>
			<span class="description"><?php echo esc_html($parameters['description']); ?></span>
			<span class="location"><?php echo esc_html($parameters['location']); ?></span>
		</div>
		<?php

		// End output buffering and return buffered output.
		return ob_get_clean();
	}

	// Other necessary methods like button rendering, etc...
}

