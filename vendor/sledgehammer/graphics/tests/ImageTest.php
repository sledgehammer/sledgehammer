<?php
namespace Sledgehammer;
/**
 * Image test
 *
 * @package GD
 */
class ImageTest extends TestCase {

	function test_jpg() {
		$Image = new Image(dirname(__FILE__).'/images/test.jpg');
		$this->assertEquals($Image->width, 132);
	}

	function test_invalid_extension_warning() {
		$this->setExpectedException('PHPUnit_Framework_Error_Warning');
		$filename = dirname(__FILE__).'/images/jpeg_file.gif';
		$image = new Image($filename);
		$image->height;
	}

	function test_extension_autocorrection() {
		$filename = dirname(__FILE__).'/images/jpeg_file.gif';
		$image = new Image($filename);
		$this->assertEquals(@$image->width, 132);
	}

}
?>
