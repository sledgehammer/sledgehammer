<?php
/**
 * Image
 */
namespace Sledgehammer;
/**
 * An image from a .png, .jpg, .gif, or .bmp file.
 *
 * Lazily loads an image file
 *
 * @package Graphics
 */
class Image extends Graphics {

	/**
	 * @var string Path to the imagefile
	 */
	private $filename;

	function __construct($filename) {
		$this->filename = $filename;
	}

	protected function rasterize() {
		if ($this->gd === null) {
			$mimetype = mimetype($this->filename, true, 'UNKNOWN');
			if ($mimetype == 'UNKNOWN' || $this->createFromMimetype($mimetype) == false) {
				$imageInfo = getimagesize($this->filename);
				if (isset($imageInfo['mime'])) {
					$detectedMimetype = $imageInfo['mime'];
				} else {
					notice('Imagetype detection failed');
					$detectedMimetype = false;
				}
				if ($detectedMimetype === false || $this->createFromMimetype($detectedMimetype) == false) {
					throw new \Exception('Unable to load "'.$this->filename.'"');
				}
				if ($mimetype != 'UNKNOWN') {
					notice('Invalid extension, detected mimetype: "'.$detectedMimetype.'" for "'.$this->filename.'"');
				}
			}
		}
		return $this->gd;
	}

	private function createFromMimetype($mimetype) {
		$mimetype_to_function = array(
			'image/png' => 'imagecreatefrompng',
			'image/gif' => 'imagecreatefromgif',
			'image/jpeg' => 'imagecreatefromjpeg',
			'image/bmp' => 'imagecreatefrombmp',
		);
		if (isset($mimetype_to_function[$mimetype])) {
			$function = $mimetype_to_function[$mimetype];
			$this->gd = $function($this->filename);
		} else {
			notice('Unsupported mimetype: "'.$mimetype.'" for "'.$this->filename.'"');
		}
		if ($this->gd) {
			return true;
		}
		return false;
	}
}

?>
