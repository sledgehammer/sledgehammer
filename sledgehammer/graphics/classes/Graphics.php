<?php
/**
 * Graphics
 */
namespace Sledgehammer;
/**
 * The Graphics class, the baseclass for all Graphics classes.
 *
 * Basic graphics operations: resizing, cropping, generating thumbnails, etc.
 * Compatible with the View interface.
 *
 * @property int $width in pixels.
 * @property int $height in pixels.
 * @property-read float $aspectRatio
 *
 * @package Graphics
 */
class Graphics extends Object {

	/**
	 * @var resource GD
	 */
	protected $gd;

	/**
	 * @param resource $gd
	 */
	function __construct($gd) {
		if (!function_exists('gd_info')) {
			throw new \Exception('Required PHP extension "GD" is not loaded');
		}

		$this->gd = $gd;
	}

	function __destruct() {
		if ($this->gd !== null) {
			imagedestroy($this->gd);
		}
	}

	/**
	 * Returns a new Graphics object in the given size.
	 *
	 * @param int $width
	 * @param int $height
	 * @return Image
	 */
	function resized($width, $height) {
		$gd = $this->rasterizeTruecolor();
		$resized = $this->createCanvas($width, $height);
		imagecopyresampled($resized, $gd, 0, 0, 0, 0, $width, $height, imagesx($gd), imagesy($gd));
		return new Graphics($resized);
	}

	/**
	 * Return a new Graphics object with the cropped contents.
	 * Resizes the canvas, but not the contents.
	 *
	 * @param int $width  The new width
	 * @param int $height  The new height
	 * @param int $offsetLeft  Offset left (null: centered)
	 * @param int $offsetTop  Offset top (null: centered)
	 * @return Graphics
	 */
	function cropped($width, $height, $offsetLeft = null, $offsetTop = null) {
		$gd = $this->rasterizeTruecolor();
		$top = 0;
		$left = 0;
		$sourceWidth = imagesx($gd);
		$sourceHeight = imagesy($gd);

		if ($offsetLeft === null || $offsetTop === null) {
			if ($offsetLeft === null) { // horizontal-align center?
				$offsetLeft = floor(($sourceWidth - $width) / 2.0);
			}
			if ($offsetTop === null) { // vertical-align center?
				$offsetTop = floor(($sourceHeight - $height) / 2.0);
			}
		}
		$cropped = $this->createCanvas($width, $height);
		if ($offsetTop < 0) {
			$top = -1 * $offsetTop;
			$offsetTop = 0;
		}
		if ($offsetLeft < 0) {
			$left = -1 * $offsetLeft;
			$offsetLeft = 0;
		}
		imagecopy($cropped, $gd, $left, $top, $offsetLeft, $offsetTop, $sourceWidth, $sourceHeight);
		return new Graphics($cropped);
	}

	/**
	 * Return a new Graphics object in te given rotation
	 *
	 * @param float $angle
	 * @param string $bgcolor
	 * @return Graphics
	 */
	function rotated($angle, $bgcolor = 'rgba(255,255,255,0)') {
		$gd = $this->rasterizeTruecolor();
		$rotated = imagerotate($gd, $angle, $this->colorIndex($bgcolor, $gd));
		return new Graphics($rotated);
	}

	function __get($property) {
		switch ($property) {

			case 'width':
			case 'height':
			case 'aspectRatio':
				$method = 'get'.ucfirst($property);
				return $this->$method();
		}
		return parent::__get($property);
	}

	/**
	 * Save the image
	 *
	 * @param string $filename
	 * @param array $options
	 */
	function saveTo($filename, $options = array()) {
		$defaults = array(
			'mimetype' => null,
			'quality' => 85, // (jpeg)
		);
		$options = $options + $defaults;
		$error = 'Failed to save the image to "'.$filename.'"';
		$mimetype = $options['mimetype'];
		if ($filename === null) {
			$error = 'Failed to render the image';
		} elseif ($mimetype === null) {
			$mimetype = mimetype($filename);
		}
		if ($mimetype === 'image/jpeg') {
			if (!imagejpeg($this->rasterize(), $filename, $options['quality'])) {
				throw new \Exception($error);
			}
			return;
		}
		$mimetype_to_function = array(
			'image/png' => 'imagepng',
			'image/gif' => 'imagegif',
		);
		if (isset($mimetype_to_function[$mimetype])) {
			$function = $mimetype_to_function[$mimetype];
			if (!$function($this->rasterize(), $filename)) {
				throw new \Exception($error);
			}
		} else {
			warning('Unsupported mimetype: "'.$mimetype.'"');
		}
	}

	/**
	 * Create a thumbnail
	 *
	 * @param string $filename
	 * @param int $width
	 * @param int $height
	 */
	function saveThumbnail($filename, $width, $height) {
		$sourceWidth = $this->width;
		$sourceHeight = $this->height;
		$ratio = $width / $height;

		$diff = $ratio - ($sourceWidth / $sourceHeight);
		if ($diff < 0) {
			$diff *= -1;
		}
		if ($diff < 0.1) { // Ignore small changes in aspect ratio
			$cropped = $this;
		} else {
			// Crop image to correct aspect ratio
			if ($ratio * $sourceHeight < $sourceWidth) { // Discard a piece from the left & right
				$cropped = $this->cropped(round($ratio * $sourceHeight), $sourceHeight);
			} else { // Discard a piece from the top & bottom
				$cropped = $this->cropped($sourceWidth, round($sourceWidth / $ratio));
			}
		}
		$thumbnail = $cropped->resized($width, $height);
		$options = array(
			'quality' => 75
		);
		if ($width < 200) { // Small thumbnail?
			$options['quality'] = 60;
		}
		$thumbnail->saveTo($filename, $options);
	}

	// View/Document interface functions: isDocument(), getHeaders() & render()

	/**
	 * This View can not be nested inside another view.
	 *
	 * @return true
	 */
	function isDocument() {
		return true;
	}

	function getHeaders() {
		return array(
			'http' => array('Content-Type' => 'image/png')
		);
	}

	function render() {
		$this->saveTo(null, array('mimetype' => 'image/png'));
	}

	/**
	 * Render the graphics to the given $gd resource.
	 *
	 * @param resource $gd
	 */
	protected function rasterizeTo($gd, $x, $y) {
		imagecopy($gd, $this->rasterizeTruecolor(), $x, $y, 0, 0, $this->width, $this->height);
	}

	/**
	 * Rasterize the layer to a truecolor(32bit) GD resource.
	 * Updates and returns the internal gd resource.
	 *
	 * @return resource gd
	 */
	protected function rasterizeTruecolor() {
		$gd = $this->rasterize();
		if (imageistruecolor($gd)) {
			return $gd;
		}
		$height = imagesy($gd);
		$width = imagesx($gd);
		$this->gd = $this->createCanvas($width, $height);
		imagecopy($this->gd, $gd, 0, 0, 0, 0, $width, $height);
		imagedestroy($gd);
		return $this->gd;
	}

	/**
	 * Rasterize the layer to an GD resource.
	 * Updates and returns the internal gd resource
	 *
	 * @return resource gd
	 */
	protected function rasterize() {
		if ($this->gd === null) {
			notice(get_class($this).'->rasterize() failed');
			// return a nixel (transparent pixel)
			$this->gd = $this->createCanvas(1,1);
		}
		return $this->gd;
	}

	/**
	 * @return int Width
	 */
	protected function getWidth() {
		return imagesx($this->rasterize());
	}

	/**
	 * @return int Height
	 */
	protected function getHeight() {
		return imagesy($this->rasterize());
	}

	/**
	 * @return float  Aspect ratio
	 */
	protected function getAspectRatio() {
		$gd = $this->rasterize();
		return imagesx($gd) / imagesy($gd);
	}

	/**
	 * Create a transparent gd resource with full (white) alphachannel
	 *
	 * @param int $width
	 * @param int $height
	 * @return resource gd
	 */
	protected function createCanvas($width, $height, $bgcolor = 'rgba(255,255,255,0)') {
		$gd = imagecreatetruecolor($width, $height);
		imagealphablending($gd, false);
		imagefilledrectangle($gd, 0, 0, $width, $height, $this->colorIndex($bgcolor, $gd));
		imagealphablending($gd, true);
		imagesavealpha($gd, true);
		return $gd;
	}

	/**
	 * Resolve/Allocate palete index for the given $color
	 *
	 * @param string $color Allowed syntax:
	 * 	'red'
	 *  '#f00'
	 *  '#ff0000'
	 *  'rgb(255, 0, 0)'
	 *  'rgba(255, 0, 0, 0.5)'
	 *
	 * @param $gd (optional) GD resource
	 * @return int
	 */
	protected function colorIndex($color, $gd = null) {
		if ($gd === null) {
			$gd = $this->gd;
		}
		$color = strtolower($color);
		$colorNames = array(
			'black' => '000000',
			'red' => 'ff0000',
			'lime' => '00ff00',
			'blue' => '0000ff',
			'yellow' => 'ffff00',
			'aqua' => '00ffff',
			'fuchsia' => 'ff00ff',
			'white' => 'ffffff',
			'silver' => 'c0c0c0',
			'gray' => '808080',
			'purple' => '800080',
			'maroon' => '800000',
			'green' => '008000',
			'olive' => '808000',
			'navy' => '000080',
			'teal' => '008080',
		);
		if (isset($colorNames[$color])) {
			$color = '#'.$colorNames[$color];
		}
		if (preg_match('/^#([0-9abcdef]{6})$/', $color, $match)) {
			// #ffffff notatie
			$red = hexdec(substr($match[1], 0, 2));
			$green = hexdec(substr($match[1], 2, 2));
			$blue = hexdec(substr($match[1], 4, 2));
		} elseif (preg_match('/^#([0-9abcdef]{3})$/', $color, $match)) {
			// #fff notatie?
			$red = hexdec(substr($match[1], 0, 1)) * 16;
			$green = hexdec(substr($match[1], 1, 1)) * 16;
			$blue = hexdec(substr($match[1], 2, 1)) * 16;
		} elseif (preg_match('/^\s{0,}rgb\s{0,}\(\s{0,}([0-9]+)\s{0,},\s{0,}([0-9]+)\s{0,},\s{0,}([0-9]+)\s{0,}\)\s{0,}$/', $color, $match)) {
			// rgb(255, 255, 255) notation
			$red = $match[1];
			$green = $match[2];
			$blue = $match[3];
		} elseif (preg_match('/^\s{0,}rgba\s{0,}\(\s{0,}([0-9]+)\s{0,},\s{0,}([0-9]+)\s{0,},\s{0,}([0-9]+)\s{0,},\s{0,}(0|1|[01]{0,1}\.[0-9]+)\s{0,}\)\s{0,}$/', $color, $match)) {
			// rgba(255, 255, 255, 0.5) notation
			$red = $match[1];
			$green = $match[2];
			$blue = $match[3];
			$alpha = ceil((1 - $match[4]) * 127);
			$pallete = imagecolorexactalpha($gd, $red, $green, $blue, $alpha); // Resolve color
			if ($pallete !== -1) {
				return $pallete;
			}
			return imagecolorallocatealpha($gd, $red, $green, $blue, $alpha); // Allocate color
		} else {
			notice('Unsupported color notation: "'.$color.'"');
			return -1;
		}
		$pallete = imagecolorexact($gd, $red, $green, $blue);  // Resolve color
		if ($pallete !== -1) {
			return $pallete;
		}
		return imagecolorallocate($gd, $red, $green, $blue); // Allocate color
	}

}

?>