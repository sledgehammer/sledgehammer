<?php
/**
 * GeoIPTest
 */
namespace Sledgehammer;
/**
 * @package GeoIP
 */
class GeoIPTest extends TestCase {

	function test_local_network() {
		if (empty($_SERVER['SERVER_ADDR'])) {
			$this->markTestSkipped('Client IP unknown');
		}
		$geo = new GeoIP();
		$this->assertTrue($geo->isLocalNetwork('127.0.0.1'));
		$this->assertTrue($geo->isLocalNetwork($_SERVER['SERVER_ADDR']));
		$this->assertFalse($geo->isLocalNetwork(gethostbyname('google.com')));
	}

	function test_country() {
		$geo = new GeoIP();
		$nl = gethostbyname('nu.nl'); // IP in the Netherlands
		$us = gethostbyname('google.com'); // IP in the US
		$this->assertEquals($geo->getCountry($nl), array(
			'code' => 'NL',
			'country' => 'Netherlands',
		));
		$this->assertEquals($geo->getCountry($us), array(
			'code' => 'US',
			'country' => 'United States',
		));
		$this->assertTrue($geo->inCountry('NL', $nl));
		$this->assertTrue($geo->inCountry('Netherlands', $nl));
		$this->assertFalse($geo->inCountry('US', $nl));
		$this->assertFalse($geo->inCountry('NL', $us));
		$this->assertTrue($geo->inCountry('US', $us));
		$this->assertTrue($geo->inCountry('United States', $us));
	}

}

?>
