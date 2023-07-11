<?php
class Test_AddEventButton extends WP_UnitTestCase {
	public function test_render_shortcode() {
		$result = do_shortcode('[addevent_button]');
		$this->assertContains('Add to Calendar', $result);
	}

	// More tests...
}
