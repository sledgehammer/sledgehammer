<?php
/**
 * Test the implementation of MVC's global functions
 *
 * @package MVC
 */
namespace Sledgehammer;

class MvcFunctionsTest extends TestCase {

	function test_explode_xml_parmeters() {
		// array( $tag => $expected_result
		$expectations = array(
			'<img src="cms_images/DSCF4821.JPG" height="240" width="320">' => array(
				'src' => 'cms_images/DSCF4821.JPG',
				'height' => '240',
				'width' => '320',
			),
			'<img src="cms_images/DSCF4821.JPG" height=240>' => array(
				'src' => 'cms_images/DSCF4821.JPG',
				'height' => '240',
			),
			'src="sd" h240>' => array(
				'src' => 'sd',
			)
		);
		foreach ($expectations as $tag => $expectation) {
			$this->assertEquals($expectation, explode_xml_parameters($tag));
		}
	}

}

?>
