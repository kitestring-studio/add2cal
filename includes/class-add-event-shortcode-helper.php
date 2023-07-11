<?php

namespace AddEvent;

class Add_Event_Shortcode_Helper {

	protected $errors = [];

	/**
	 * Normalize attributes
	 */
	public function normalize_attributes($attrs) {
		$normalized = [];
		foreach ($attrs as $key => $value) {
			$normalized[strtolower($key)] = trim($value);
		}

		if (isset($normalized['24h']) && $normalized['24h'] === 'true') {
			$normalized['24h'] = true;
		} else {
			$normalized['24h'] = false;
		}

		return $normalized;
	}

	/**
	 * Validate attributes
	 */
	public function validate_attributes($attrs) {
		$this->errors = [];
		// Validate date
		if (isset($attrs['start']) && !strtotime($attrs['start'])) {
			$this->errors[] = "Invalid start date format";
		}

		if (isset($attrs['end']) && !strtotime($attrs['end'])) {
			$this->errors[] = "Invalid end date format";
		}

		if (isset($attrs['length']) && !preg_match('/^[0-9]+[mhdw]$/', $attrs['length'])) {
			$this->errors[] = "Invalid length format";
		}

		// Add more validation rules as needed

		return $this->errors;
	}

	/**
	 * Generate error message for invalid attributes
	 */
	public function get_invalid_message() {
		return implode(', ', $this->errors);
	}

	/**
	 * Generate button markup for 'addevent_button' shortcode.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @return string Generated markup.
	 */
	public function generate_button_markup( $data ) {

		$html  = '<div title="' . esc_attr( $data['title'] ) . '" class="' . esc_attr( $data['class'] ) . '">';
		$html .= esc_html( $data['title'] );
		$html .= $this->generate_date_span( 'start', $data['start'], $data['24h'] );
		if ( ! empty( $data['end'] ) ) {
			$html .= $this->generate_date_span( 'end', $data['end'], $data['24h'] );
		}
		$html .= $this->generate_span( 'timezone', $data['timezone'] );
		$html .= $this->generate_span( 'title', $data['event_title'] );
		$html .= $this->generate_span( 'description', $data['description'] );
		$html .= $this->generate_span( 'location', $data['location'] );
		$html .= '</div>';

		return $html;
	}


	/**
	 * Generate a series of add-to-calendar links for various services.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Generated markup.
	 */
	public function generate_links_markup( $atts ) {
		$markup = '';

		$base_url = 'https://www.addevent.com/dir/?client=' . ADDEVENT_API_KEY;

		$query = http_build_query([
			'start' => date('d-m-Y h:i A', strtotime($atts['start'])),
			'end' => date('d-m-Y h:i A', strtotime($atts['end'])),
			'title' => $atts['title'],
			'description' => $atts['description'],
			'location' => $atts['location'],
			'timezone' => $atts['timezone']
		], '', '&', PHP_QUERY_RFC3986);

		$services = isset($atts['services']) ? explode(',', $atts['services']) : ['apple', 'google', 'office365', 'outlook', 'outlookcom', 'yahoo'];

		foreach ($services as $service) {
			if( $service === "outlookcom") {
				$service = "outlook.com";
			}
			$service_url = $base_url . '&' . $query . '&service=' . urlencode(trim($service));
			$markup .= '<a href="' . esc_url($service_url) . '">' . esc_html(ucfirst($service)) . '</a> ';
		}

		return $markup;
	}


	/**
	 * Generate span markup for a given field.
	 *
	 * @param string $field The field name.
	 * @param string $value The field value.
	 * @return string Generated markup.
	 */
	public function generate_span( $field, $value ) {
		return '<span class="' . esc_attr( $field ) . '">' . esc_html( $value ) . '</span>';
	}

	/**
	 * Generate span markup for a date field, using the proper formatting.
	 *
	 * @param string $field The field name.
	 * @param string $date The date value.
	 * @param bool $is_24h If true, use 24-hour format. If false, use 12-hour format.
	 * @return string Generated markup.
	 */
	public function generate_date_span( $field, $date, $is_24h ) {
		$format = $is_24h ? 'Y-m-d H:i' : 'm/d/Y h:i A';
		$formatted_date = date( $format, strtotime( $date ) );
		return '<span class="' . esc_attr( $field ) . '">' . esc_html( $formatted_date ) . '</span>';
	}

}
