<?php

namespace AddEvent;

class AddEventButton extends AddEventBase {
	public static function render_shortcode($atts) {
		$parameters = shortcode_atts(self::get_shortcode_defaults(), $atts);
		try {
			self::validate_parameters($parameters);
		} catch (Exception $e) {
			return $e->getMessage(); // This will replace the shortcode with the error message.
		}

		// Proceed with the button rendering...
	}
}

