<?php
/**
 * Canvas
 */
namespace Sledgehammer;
/**
 * Drawing lines, rectangles, etc
 *
 * @package Graphics
 */
class Canvas extends Graphics {

	/**
	 * @var int  The index for the current color, (default: black)
	 */
	private $color;

	function __construct($width, $height, $bgcolor = 'rgba(255,255,255,0)') {
		parent::__construct($this->createCanvas($width, $height, $bgcolor));
		// Set brush color to opaque black
		$this->color = imagecolorallocate($this->gd, 0, 0, 0);
	}

	/**
	 * Set the thickness for line drawing
	 * @param int $px
	 */
	function setThickness($px) {
		imagesetthickness($this->gd, $px);
	}

	/**
	 * Set the Brush color to the given $color
	 *
	 * @param string $color Allowed syntax:
	 * 	'red'
	 *  '#f00'
	 *  '#ff0000'
	 *  'rgb(255, 0, 0)'
	 *  'rgba(255, 0, 0, 0.5)'
	 *
	 * @return void
	 */
	function setColor($color) {
		$this->color = $this->colorIndex($color);
	}

	/**
	 * Vul de gehele gd met de meegegeven kleur
	 * @param string $color  Bv: 'ddeeff'
	 */
	function fill($color = null) {
		imagefilledrectangle($this->gd, 0, 0, $this->width, $this->height, $this->color($color));
	}

	function dot($x, $y, $color = null) {
		imagesetpixel($this->gd, $x, $y, $this->color($color));
	}

	function line($x1, $y1, $x2, $y2, $color = null) {
		imageline($this->gd, $x1, $y1, $x2, $y2, $this->color($color));
	}

	function rectangle($x, $y, $width, $height, $color = null) {
		imagerectangle($this->gd, $x, $y, $x + $width - 1, $y + $height - 1, $this->color($color));
	}

	function fillRectangle($x, $y, $width, $height, $color = null) {
		imagefilledrectangle($this->gd, $x, $y, $x + $width - 1, $y + $height - 1, $this->color($color));
	}

	/**
	 * Return a color palette index
	 *
	 * @param null|string $color null: Current color, string: html-color-code
	 * @return int
	 */
	private function color($color = null) {
		if ($color === null) {
			return $this->color;
		}
		return $this->colorIndex($color);
	}
}

?>
