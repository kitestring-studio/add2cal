<?php

namespace AddEvent;
//use Addevent\Add_Event;

class Add_Event_Shortcodes {

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->shortcode_helper = new Add_Event_Shortcode_Helper();
	}


	/**
	 * Render 'addevent_links' shortcode
	 */
	public function addevent_links_shortcode( $atts, $content, $shortcode_tag ) {
		if ( $shortcode_tag === "addevent_links" && ! defined( 'ADDEVENT_API_KEY' ) ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'button_label' => 'Add to Calendar',
			'class'        => 'addeventatc',
			'start'        => '',
//			'end'          => '',
			'length'       => '1h',
			'24h'          => '',
			'title'        => '',
			'timezone'     => '',
			'description'  => '',
			'location'     => '',
		), $atts );

		$type = $shortcode_tag === 'addevent_button' ? 'button' : 'links';

		$atts = $this->shortcode_helper->normalize_attributes( $atts );

		if ( count( $this->shortcode_helper->validate_attributes( $atts ) ) ) {
			return $this->shortcode_helper->get_invalid_message();
		}

		Add_Event::enqueue_scripts();
		$atts = $this->shortcode_helper->post_process_attributes( $atts, $shortcode_tag );

		return $this->shortcode_helper->{"generate_{$type}_markup"}( $atts );
	}
}
