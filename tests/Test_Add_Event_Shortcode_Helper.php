<?php
/**
 * Class Test_Add_Event_Shortcode_Helper
 *
 * @package Add_Event
 */
use PHPUnit\Framework\TestCase;
use AddEvent\Add_Event_Shortcode_Helper;

//require_once '../vendor/autoload.php';
// require autoload.php from the root of the plugin
require_once __DIR__ . '/../vendor/autoload.php';
/**
 * Test case for class Add_Event_Shortcode_Helper.
 */
class Test_Add_Event_Shortcode_Helper extends WP_UnitTestCase {
	private $instance;

	public function setUp(): void {
		parent::setUp();
		$this->instance = new AddEvent\Add_Event_Shortcode_Helper();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_normalize_attributes() {
		$atts = [
			'start' => '2023-12-25 00:00',
			'end' => '2023-12-26 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'24h' => 'false',
			'class' => 'my-class'
		];
		$normalized = $this->instance->normalize_attributes($atts);
		$this->assertIsArray($normalized);
		$this->assertArrayHasKey('start', $normalized);
		$this->assertArrayHasKey('end', $normalized);
		$this->assertArrayHasKey('title', $normalized);
		$this->assertArrayHasKey('description', $normalized);
		$this->assertArrayHasKey('location', $normalized);
		$this->assertArrayHasKey('24h', $normalized);
		$this->assertArrayHasKey('class', $normalized);
		$this->assertIsBool($normalized['24h']);
	}

	public function test_validate_attributes() {
		$atts = [
			'start' => strtotime('2023-12-25 00:00'),
			'end' => strtotime('2023-12-26 00:00'),
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'length' => '1d'
		];
		$errors = $this->instance->validate_attributes($atts);
		$this->assertIsArray($errors);
		$this->assertCount(0, $errors);
	}

	public function test_post_process_attributes() {
		$atts = [
			'start' => strtotime('2023-12-25 00:00'),
			'end' => strtotime('2023-12-26 00:00'),
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'length' => '1d',
			'class' => 'my-class'
		];
		$processed = $this->instance->post_process_attributes($atts, 'addevent_button');
		$this->assertIsArray($processed);
		$this->assertArrayHasKey('start', $processed);
		$this->assertArrayHasKey('end', $processed);
		$this->assertArrayHasKey('title', $processed);
		$this->assertArrayHasKey('description', $processed);
		$this->assertArrayHasKey('location', $processed);
	}

	public function test_generate_button_markup() {
		$atts = [
			'start' => '2023-12-25 00:00',
			'end' => '2023-12-26 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'button_label' => 'Add to Calendar',
			'class' => 'my-class'
		];
		$markup = $this->instance->generate_button_markup($atts);
		$this->assertStringContainsString('<div', $markup);
		$this->assertStringContainsString('addeventatc', $markup);
		$this->assertStringContainsString('my-class', $markup);
	}

	public function test_generate_links_markup() {
		$atts = [
			'start' => '2023-12-25 00:00',
			'end' => '2023-12-26 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'services' => 'google,outlook'
		];
		$markup = $this->instance->generate_links_markup($atts);
		$this->assertStringContainsString('<a href', $markup);
		$this->assertStringContainsString('Google', $markup);
		$this->assertStringContainsString('Outlook', $markup);
	}

	public function test_validate_attributes_with_invalid_data() {
		$atts = [
			'start' => 'invalid date',
			'end' => '2023-12-26 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'length' => '1d'
		];
		$errors = $this->instance->validate_attributes($atts);
		$this->assertIsArray($errors);
		$this->assertCount(1, $errors);
	}

	public function test_generate_links_markup_with_invalid_service() {
		$atts = [
			'start' => '2023-12-25 00:00',
			'end' => '2023-12-26 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'services' => 'invalid-service'
		];
		$markup = $this->instance->generate_links_markup($atts);
		$this->assertStringNotContainsString('invalid-service', $markup);
	}

	public function test_generate_button_markup_with_different_class() {
		$atts = [
			'start' => '2023-12-25 00:00',
			'end' => '2023-12-26 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'button_label' => 'Add to Calendar',
			'class' => 'different-class'
		];
		$markup = $this->instance->generate_button_markup($atts);
		$this->assertStringContainsString('different-class', $markup);
	}

	public function test_post_process_attributes_with_different_event_length() {
		$atts = [
			'start' => strtotime('2023-12-25 00:00'),
			'end' => strtotime('2023-12-26 00:00'),
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'length' => '2d',
			'class' => 'my-class'
		];
		$processed = $this->instance->post_process_attributes($atts, 'addevent_button');
		$this->assertIsArray($processed);
		$this->assertEquals('2d', $processed['length']);
	}

	public function test_start_date_after_end_date() {
		$atts = [
			'start' => '2023-12-26 00:00',
			'end' => '2023-12-25 00:00',
			'title' => 'Christmas Day',
			'description' => 'Celebrating Christmas',
			'location' => 'Everywhere',
			'length' => '1d'
		];
		$errors = $this->instance->validate_attributes($atts);
		$this->assertIsArray($errors);
		$this->assertCount(1, $errors);
	}

	public function test_integration_with_wordpress() {
		// Create a post with the shortcode
		$post_id = $this->factory->post->create( array(
			'post_content' => '[addevent start="2023-12-25 00:00" end="2023-12-26 00:00" title="Christmas Day" description="Celebrating Christmas" location="Everywhere"]'
		));

		// Get the post content
		$post = get_post($post_id);
		$content = apply_filters('the_content', $post->post_content);

		// Check that the content contains the expected markup
		$this->assertStringContainsString('<div', $content);
		$this->assertStringContainsString('addeventatc', $content);
	}
}
