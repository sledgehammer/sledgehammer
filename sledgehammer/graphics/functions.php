<?php
/**
 * Global graphics functions in the Sledgehammer namespace
 *
 * @package Graphics
 */
//
namespace Sledgehammer;

/**
 * Shorthand for creating an Image object.
 *
 * @param string $filename
 * @return Image
 */
function image($filename) {
	return new Image($filename);
}

/**
 * Create a GD resource from a BMP file.
 *
 * @link http://php.net/gd
 * @param string $filename
 * @return resource gd
 */
function imagecreatefrombmp($filename) {
	// Load the image into a string
	$fp = fopen($filename, "rb");
	$read = fread($fp, 10);
	while (!feof($fp) && ($read <> "")) {
		$read .= fread($fp, 1024);
	}

	$temp = unpack("H*", $read);
	$hex = $temp[1];
	$header = substr($hex, 0, 108);

	// Process the header
	// Structure: http://www.fastgraph.com/help/bmp_header_format.html
	if (substr($header, 0, 4) == "424d") {

		$header_parts = str_split($header, 2); // Cut it in parts of 2 bytes
		$width = hexdec($header_parts[19].$header_parts[18]); // Get the width (4 bytes)
		$height = hexdec($header_parts[23].$header_parts[22]); // Get the height (4 bytes)
		unset($header_parts);
	}
	$x = 0;
	$y = 1;

	//    Create newimage
	$image = imagecreatetruecolor($width, $height);

	// Grab the body from the image
	$body = substr($hex, 108);

	// Calculate if padding at the end-line is needed
	// Divided by two to keep overview.
	// 1 byte = 2 HEX-chars
	$body_size = (strlen($body) / 2);
	$header_size = ($width * $height);

	// Use end-line padding? Only when needed
	$usePadding = ($body_size > ($header_size * 3) + 4);

	// Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
	// Calculate the next DWORD-position in the body
	for ($i = 0; $i < $body_size; $i += 3) {
		//    Calculate line-ending and padding
		if ($x >= $width) {
			// If padding needed, ignore image-padding
			// Shift i to the ending of the current 32-bit-block
			if ($usePadding) {
				$i += $width % 4;
			}
			$x = 0; // Reset horizontal position
			$y++; // Raise the height-position (bottom-up)


			if ($y > $height) { // Reached the image-height?
				break;
			}
		}

		// Calculation of the RGB-pixel (defined as BGR in image-data)
		// Define $i_pos as absolute position in the body
		$i_pos = $i * 2;
		$r = hexdec($body[$i_pos + 4].$body[$i_pos + 5]);
		$g = hexdec($body[$i_pos + 2].$body[$i_pos + 3]);
		$b = hexdec($body[$i_pos].$body[$i_pos + 1]);

		// Calculate and draw the pixel
		$color = imagecolorallocate($image, $r, $g, $b);
		imagesetpixel($image, $x, ($height - $y), $color);

		$x++; // Raise the horizontal position
	}
	unset($body);
	return $image;
}

?>