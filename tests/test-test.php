<?php

use AddEvent\Add_Event;
use AddEvent\Add_Event_Shortcode_Helper;
use PHPUnit\Framework\TestCase;

require_once '../vendor/autoload.php';

class Test_Test extends TestCase {

	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}

	public function test_instansiate_class() {
		$helper = new Add_Event_Shortcode_Helper();
		$this->assertInstanceOf(Add_Event_Shortcode_Helper::class, $helper);
	}
}
