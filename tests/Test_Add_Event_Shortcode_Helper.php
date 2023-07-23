<?php
/**
 * Class Test_Add_Event_Shortcode_Helper
 *
 * @package Add_Event
 */
use PHPUnit\Framework\TestCase;
use AddEvent\Add_Event_Shortcode_Helper;

require_once '../vendor/autoload.php';

/**
 * Test case for class Add_Event_Shortcode_Helper.
 */
class Test_Add_Event_Shortcode_Helper extends WP_UnitTestCase {

	/**
	 * The instance of Add_Event_Shortcode_Helper that we're testing.
	 *
	 * @var Add_Event_Shortcode_Helper
	 */
	private $shortcode_helper;

	/**
	 * WP_UnitTestCase setUp runs before each test.
	 */
//	public function setUp() {
//		parent::setUp();
//		$this->shortcode_helper = new Add_Event_Shortcode_Helper();
//	}

	/**
	 * WP_UnitTestCase tearDown runs after each test.
	 */
	/*public function tearDown() {
		unset( $this->shortcode_helper );
		parent::tearDown();
	}*/

	// Tests will go here.
	// Continuation of the Test_Add_Event_Shortcode_Helper class

	/**
	 * Test normalize_attributes method.
	 */
	public function test_normalize_attributes() {
		$input = array(
			'start' => ' 10-07-2023  2:00 PM ',
			'end'   => ' 10-07-2023  4:00 PM ',
			'24h'   => ' TRUE ',
		);
		$expected = array(
			'start' => '10-07-2023 2:00 PM',
			'end'   => '10-07-2023 4:00 PM',
			'24h'   => true,
		);

		$this->assertEquals(
			$expected,
			$this->shortcode_helper->normalize_attributes($input)
		);
	}

	public function test_true_equals_true() {
		$this->shortcode_helper = new Add_Event_Shortcode_Helper();

		$this->assertTrue(true);
	}

	/**
	 * Test validate_attributes method.
	 */
	public function test_validate_attributes() {
		// Test a valid input.
		$input = array(
			'start' => '10-07-2023 2:00 PM',
			'end'   => '10-07-2023 4:00 PM',
			'24h'   => true,
		);
		$this->assertTrue($this->shortcode_helper->validate_attributes($input));

		// Test an invalid input.
		$input = array(
			'start' => '10-07-2023 2:00 PM',
			'end'   => '09-07-2023 4:00 PM',
			'24h'   => true,
		);
		$this->assertFalse($this->shortcode_helper->validate_attributes($input));
	}

// More tests will go here.

// End of the Test_Add_Event_Shortcode_Helper class

}
