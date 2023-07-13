<?php

namespace AddEvent;

class Add_Event_Shortcode_Helper {

	protected $errors = [];

	/**
	 * Normalize attributes
	 */
	public function normalize_attributes( $attrs ) {
		$normalized = [];
		foreach ( $attrs as $key => $value ) {
			$normalized[ strtolower( $key ) ] = trim( sanitize_text_field( $value ) );
		}

		if ( isset( $normalized['24h'] ) && $normalized['24h'] === 'true' ) {
			$normalized['24h'] = true;
		} else {
			$normalized['24h'] = false;
		}

		if ( ! isset( $normalized['class'] ) ) {
			$normalized['class'] = '';
		}


		if ( isset( $normalized['start'] ) ) {
			$normalized['start'] = $this->normalize_date( $normalized['start'] );
		}

		if ( isset( $normalized['end'] ) ) {
			$normalized['end'] = $this->normalize_date( $normalized['end'] );
		}

		if ( isset( $normalized['length'])) {
			// length is a string like '1d' or '2w'. Convert to '1 day' or '2 weeks'
//			$normalized['length'] = $this->normalize_length( $normalized['length'] );
		}

		// @TODO $normalized['service'] must either be blank, or a combination of the following:
		// 'apple', 'google', 'outlook', 'outlookcom', 'yahoo'


		return $normalized;
	}

	/**
	 * Normalize date
	 */
	public function normalize_date( $date ) {
		// detect the format of the date. If it's not a timestamp, convert it to a timestamp
		if ( ! is_numeric( $date ) ) {
			$date = strtotime( $date );
		}
		return $date;
	}

	/**
	 * Validate attributes
	 */
	public function validate_attributes($attrs) {
		$this->errors = [];
		// Validate date
		if ( isset( $attrs['start'] ) && ! $this->is_timestamp( $attrs['start'] ) ) {
			$this->errors[] = "Invalid start date format";
		}

		if ( isset( $attrs['end'] ) && ! $this->is_timestamp( $attrs['end'] ) ) {
			$this->errors[] = "Invalid end date format";
		}

		if ( isset( $attrs['length'] ) && ! preg_match( '/^[0-9]+[mhdw]$/', $attrs['length'] ) ) {
			$this->errors[] = "Invalid length format";
		}

		// Add more validation rules as needed

		return $this->errors;
	}

	public function is_timestamp( $timestamp ) {
		return ( is_numeric( $timestamp ) && strtotime( date( 'Y-m-d H:i:s', $timestamp ) ) === (int) $timestamp );
	}

	public function post_process_attributes( $attrs ) {
		// If length is set, calculate end date
		if ( isset( $attrs['length'] ) ) {
			$attrs['end'] = $this->calculate_end_date( $attrs['start'], $attrs['length'] );
		}

		$attrs['start'] = $this->format_date( $attrs['start'] );
		$attrs['end']   = $this->format_date( $attrs['end'] );


		unset( $attrs['length'] );

		return $attrs;
	}

	protected function calculate_end_date( $start, $length ) {
		// generate $end from $start and $length, where $length is a string like '1d' or '2w' and $start is timestamp
		$end = $start;
		$length = strtolower( $length );
		$unit = substr( $length, -1 );
		$amount = substr( $length, 0, -1 );
		switch ( $unit ) {
			case 'm':
				$end = strtotime( '+' . $amount . ' minutes', $start );
				break;
			case 'h':
				$end = strtotime( '+' . $amount . ' hours', $start );
				break;
			case 'd':
				$end = strtotime( '+' . $amount . ' days', $start );
				break;
			case 'w':
				$end = strtotime( '+' . $amount . ' weeks', $start );
				break;
		}


		return $end;
	}

	/**
	 * Generate error message for invalid attributes
	 */
	public function get_invalid_message() {
		return implode( ', ', $this->errors );
	}

	/**
	 * Generate button markup for 'addevent_button' shortcode.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @return string Generated markup.
	 */
	public function generate_button_markup( $data ) {

		$html = '<div title="' . esc_attr( $data['button_label'] ) . '" class="addeventatc ' . esc_attr( $data['class'] ) . '">';
//		$html .= esc_html( $data['button_label'] );

		foreach ( $data as $key => $value ) {
			$html .= $this->generate_span( $key, $value );
		}

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

		$query = http_build_query( $atts, '', '&', PHP_QUERY_RFC3986 );

		$services = isset( $atts['services'] ) ? explode( ',', $atts['services'] ) : [
			'apple',
			'google',
			'office365',
			'outlook',
			'outlookcom',
			'yahoo'
		];

		foreach ( $services as $service ) {
			if ( $service === "outlookcom" ) {
				$service = "outlook.com";
			}
			$service_url = $base_url . '&' . $query . '&service=' . urlencode( trim( $service ) );
			$markup      .= '<a href="' . esc_url( $service_url ) . '" target="_blank">' . esc_html( ucfirst( $service ) ) . '</a> ';
		}

		return "<div class='addevent__links'>$markup</div>";
	}


	/**
	 * Generate span markup for a given field.
	 *
	 * @param string $field The field name.
	 * @param string $value The field value.
	 * @return string Generated markup.
	 */
	public function generate_span( $field, $value ): string {
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
	public function generate_date_span( $field, $date, $is_24h ): string {
		$format         = $is_24h ? 'Y-m-d H:i' : 'm/d/Y h:i A';
		$formatted_date = date( $format, $date );

		return '<span class="' . esc_attr( $field ) . '">' . esc_html( $formatted_date ) . '</span>';
	}

	public function format_date( int $date ): string {
		$format ='Y-m-d H:i'; // ISO 8601

		return date( $format, $date );
	}

	/**
	 * @param $length
	 *
	 * @return array|string|string[]|null
	 */
	protected function normalize_length( $length ) {
		return preg_replace_callback( '/([0-9]+)([mhdw])/', function ( $matches ) {
			$unit = $matches[2];
			switch ( $unit ) {
				case 'm':
					$unit = ' minutes';
					break;
				case 'h':
					$unit = ' hours';
					break;
				case 'd':
					$unit = ' days';
					break;
				case 'w':
					$unit = ' weeks';
					break;
			}

			return $matches[1] . $unit;
		}, $length );
	}

}
