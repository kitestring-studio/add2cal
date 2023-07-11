<?php

namespace AddEvent;

class Add_Event_Admin_Notices {

	/**
	 * Display admin notices
	 */
	public static function display_admin_notices() {
		if ( ! defined( 'ADDEVENT_API_KEY' ) ) {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo __( 'The AddEvent plugin requires the AddEvent API key to be defined in your wp-config.php file.', 'addevent' ); ?></p>
            </div>
			<?php
		}
	}
}
