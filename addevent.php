<?php

/**
 * Plugin Name: Addevent Shortcode
 * Plugin URI: https://yourwebsite.com/addevent-shortcode
 * Description: This plugin provides a shortcode to generate the addevent button.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL2
 */

class AddEventUtility {
	static function get_api_key() {
		return defined('ADDEVENT_API_KEY') ? ADDEVENT_API_KEY : '';
	}

	static function format_datetime($datetime, $is_24h = false) {
		$format = $is_24h ? 'd-m-Y H:i' : 'm/d/Y h:i A';
		return date($format, strtotime($datetime));
	}

	static function calculate_endtime($start, $length) {
		// Omitted for brevity: logic for calculating end time based on length.
	}

	static function encode_parameters($parameters) {
		return array_map('urlencode', $parameters);
	}
}
class Addevent_Button {
	public function __construct() {
		add_shortcode( 'addevent', array( $this, 'generate_shortcode' ) );
		add_shortcode( 'addevent_button', array( $this, 'generate_shortcode' ) );
		add_action( 'wp_footer', array( $this, 'print_addevent_script' ) );
	}

	public function generate_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'title'       => 'Add to Calendar',
			'class'       => 'addeventatc',
			'start'       => '',
			'end'         => '',
			'length'      => '',
			'24h'         => false,
			'timezone'    => '',
			'event_title' => '',
			'description' => '',
			'location'    => '',
		), $atts );

		// Validate date and time
		$date_format = ( isset( $atts['24h'] ) && strtolower( $atts['24h'] ) === 'true' ) ? 'd-m-Y H:i' : 'm/d/Y h:i A';
		$start       = DateTime::createFromFormat( $date_format, $atts['start'] );
		if ( ! $start ) {
			return 'Invalid start date format. Please check your shortcode.';
		}

		// Enqueue script if it's not enqueued already
		if ( ! wp_script_is( 'addevent-js', 'enqueued' ) ) {
			wp_register_script( 'addevent-js', 'https://cdn.addevent.com/libs/atc/1.6.1/atc.min.js', array(), null, true );
			wp_enqueue_script( 'addevent-js' );
		}

		// Calculate end time if length is given
		if ( ! empty( $atts['length'] ) && empty( $atts['end'] ) ) {
			$length        = substr( $atts['length'], 0, - 1 );
			$unit          = substr( $atts['length'], - 1 );
			$interval_spec = '';

			switch ( $unit ) {
				case 'm':
					$interval_spec = 'PT' . $length . 'M';
					break;
				case 'h':
					$interval_spec = 'PT' . $length . 'H';
					break;
				case 'd':
					$interval_spec = 'P' . $length . 'D';
					break;
				case 'w':
					$interval_spec = 'P' . $length . 'W';
					break;
				default:
					return 'Invalid length format. Please check your shortcode.';
			}

			$interval = new DateInterval( $interval_spec );
			$end      = clone $start;
			$end->add( $interval );
		} else {
			$end = DateTime::createFromFormat( $date_format, $atts['end'] );
			if ( ! $end ) {
				return 'Invalid end date format. Please check your shortcode.';
			}
		}

		// Prepare HTML
		$html = sprintf(
			'<div title="%s" class="%s">
            Add to Calendar
            <span class="start">%s</span>
            <span class="end">%s</span>
            <span class="timezone">%s</span>
            <span class="title">%s</span>
            <span class="description">%s</span>
            <span class="location">%s</span>
        </div>',
			esc_attr( $atts['title'] ),
			esc_attr( $atts['class'] ),
			esc_html( $start->format( $date_format ) ),
			esc_html( $end->format( $date_format ) ),
			esc_html( $atts['timezone'] ),
			esc_html( $atts['event_title'] ),
			esc_html( $atts['description'] ),
			esc_html( $atts['location'] )
		);

		return $html;
	}

	public function print_addevent_script() {
		if ( wp_script_is( 'addevent-js', 'enqueued' ) ) {
			wp_print_scripts( 'addevent-js' );
		}
	}

}

class AddEventLinks {
	public function __construct() {
		add_shortcode('addevent_links', array($this, 'shortcode_callback'));
		add_action('admin_notices', array($this, 'admin_notice_missing_api_key'));
	}

	public function shortcode_callback($atts = [], $content = null) {
		$api_key = AddEventUtility::get_api_key();

		if (!$api_key) {
			if (current_user_can('manage_options')) {
				// User is an admin and can see the notice about the missing API key.
				return "<div class='error'><p>" . __('AddEvent Links: Please enter your API key on the <a href="options-general.php?page=addevent">AddEvent settings page</a>.') . "</p></div>";
			} else {
				// Fail silently for non-admin users.
				return 'fail';
			}
		}

		// Existing logic for generating add-to-calendar links.
		$start = AddEventUtility::format_datetime($atts['start'], $atts['24h'] === 'true');
		$end = isset($atts['end']) ? AddEventUtility::format_datetime($atts['end'], $atts['24h'] === 'true') :
			AddEventUtility::calculate_endtime($atts['start'], $atts['length']);

		// Prepare parameters.
		$parameters = [
			'client' => AddEventUtility::get_api_key(),
			'start' => $start,
			'end' => $end,
			'title' => $atts['title'],
			'description' => $atts['description'],
			'location' => $atts['location'],
			'timezone' => $atts['timezone']
		];

		$services = isset($atts['services']) ? explode(',', $atts['services']) : ['apple', 'google', 'office365', 'outlook', 'outlookcom', 'yahoo'];
		$output = '';
		foreach ($services as $service) {
			$parameters['service'] = $service;
			$url = 'https://www.addevent.com/dir/?' . http_build_query($parameters);
			$output .= "<a href=\"{$url}\">{$service}</a> ";
		}

		return $output;

	}

	public function admin_notice_missing_api_key() {
		$api_key = AddEventUtility::get_api_key();

		if (!$api_key && current_user_can('manage_options')) {
			echo '<div class="notice notice-error"><p>Addevent needs its client ID (API Key) defined in wp-config.php.</p></div>';
		}
	}
}

new AddEventLinks();
new Addevent_Button();
