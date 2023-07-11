<?php

namespace AddEvent;

abstract class AddEventBase {
	protected static function get_shortcode_defaults() {
		return array(
			'title' => '',
			'description' => '',
			'location' => '',
			'start' => '',
			'end' => '',
			'timezone' => '',
			'24h' => 'false',
			'length' => '',
		);
	}

	protected static function sanitize_parameters($parameters) {
		// Implementation of the sanitization logic based on the parameter types...
		// This may include regular expressions checks, strlen checks, etc.
	}

	protected static function validate_parameters($parameters) {
		// Implementation of the validation logic based on the parameter types and the specific rules...
		// This should throw an Exception with a specific error message in case of validation errors.
	}
}
