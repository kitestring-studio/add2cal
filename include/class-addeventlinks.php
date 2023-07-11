<?php

namespace AddEvent;

class AddEventLinks extends AddEventBase {
	public static function render_shortcode($atts) {
		if (!defined('ADDEVENT_API_KEY')) {
			return ''; // Silently fail on the front-end if the API key is not defined.
		}
		$parameters = shortcode_atts(self::get_shortcode_defaults(), $atts);
		try {
			self::validate_parameters($parameters);
		} catch (Exception $e) {
			return $e->getMessage(); // This will replace the shortcode with the error message.
		}

		// Proceed with the links rendering...
	}
}
