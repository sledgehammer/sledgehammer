<?php
/**
 * ImageOptimizer
 * @package Minify
 */
namespace Sledgehammer;
/**
 * Optimalisation of images.
 */
class ImageOptimizer extends Object {

	static function minify($contents, $filename) {
		mkdirs(TMP_DIR.'ImageMin/');
		$extension = strtolower(file_extension($filename));
		$tmpFile = TMP_DIR.'ImageMin/'.basename($filename);
		file_put_contents($tmpFile, $contents);
		if ($extension == 'png') {
			if (self::minifyPNG($tmpFile) === false) {
				return false;
			}
		} elseif (in_array($extension, array('jpg', 'jpeg'))) {
			if (self::minifyJPEG($tmpFile) === false) {
				return false;
			}
		} else {
			notice('Filetype: "'.$extension.'" not supported', $filename);
			return false;
		}
		$output = file_get_contents($tmpFile);
		unlink($tmpFile);
		return $output;
	}

	private static function minifyPNG($filename) {
		system(self::getCommand('optipng').' -quiet '.escapeshellarg($filename), $exit);
		if ($exit === 0) {
			return true;
		}
		return false;
	}

	private static function minifyJPEG($filename, $maxQuality = 85) {
		system(self::getCommand('jpegoptim').' --quiet --strip-all --max='.$maxQuality.' '.escapeshellarg($filename), $exit);
		if ($exit === 0) {
			return true;
		}
		return false;
	}

	private static function getCommand($executable) {
		if (file_exists('/usr/local/bin/'.$executable)) { // Is the command found in the homebrew folder?
			return '/usr/local/bin/'.$executable;
		}
		return $executable;
	}

}

?>
