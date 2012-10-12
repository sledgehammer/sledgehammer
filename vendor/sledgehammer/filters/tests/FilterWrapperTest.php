<?php
/**
 * FilterObjectTests
 *
 */
namespace Sledgehammer;

class FilterWrapperTest extends TestCase {

	function test_() {
		restore_error_handler();
		$array = array(
			'vera' => 'aloë',
			'js' => '<script>alert("hacked");</script>',
		);
		$filtered = new FilterWrapper($array, array('filter' => new HtmlFilter()));
		$this->assertEquals($filtered['vera'], 'alo&euml;');

	}

}

?>
