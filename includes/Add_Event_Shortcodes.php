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
	 * Render 'addevent_button' shortcode
	 */
	public function addevent_button_shortcode( $attrs ) {
		$attrs = $this->shortcode_helper->normalize_attributes( $attrs );

		if ( count( $this->shortcode_helper->validate_attributes( $attrs ) ) ) {
			return $this->shortcode_helper->get_invalid_message();
		}

		Add_Event::enqueue_scripts();

		return $this->shortcode_helper->generate_button_markup( $attrs );
	}

	/**
	 * Render 'addevent_links' shortcode
	 */
	public function addevent_links_shortcode( $attrs ) {
		if ( ! defined( 'ADDEVENT_API_KEY' ) ) {
			return '';
		}

		$attrs = $this->shortcode_helper->normalize_attributes( $attrs );

		if ( count( $this->shortcode_helper->validate_attributes( $attrs ) ) ) {
			return $this->shortcode_helper->get_invalid_message();
		}

		$attrs = $this->shortcode_helper->post_process_attributes( $attrs );
		Add_Event::enqueue_scripts();

		return $this->shortcode_helper->generate_links_markup( $attrs );
	}
}
