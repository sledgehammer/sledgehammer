<?php
/**
 * TextGraphics
 */
namespace Sledgehammer;
/**
 * A layer for advanced text rendering.
 * Uses CSS style options: "font: bold 14px Arial, sans-serif"
 *
 * @package Graphics
 */
class TextGraphics extends Graphics {

	/**
	 * @var string
	 */
	private $text;
	private $color = '#333';

	/**
	 * @var float fontsize in pt
	 */
	private $fontSize = 9.75; // 13px

	/**
	 * @var float line height
	 */
	private $lineHeight = 1.2;

	/**
	 * @var array Font aam van het font ("Arial", "New Times Roman", etc) of 1 - 5 voor built-in fonts
	 */
	private $fontFamily = array('sans-serif');

	/**
	 * @var string normal|bold
	 */
	private $fontWeight = 'normal';

	/**
	 * @var string normal|italic|oblique
	 */
	private $fontStyle = 'normal';

	/**
	 * @var float degrees
	 */
	private $angle = 0.0;

	/**
	 * @var string|array Allows overriding the default font style.
	 */
	static $defaultStyle = 'font: 13px/120% "DejaVu Sans", sans-serif';

	/**
	 * @var array
	 */
	static $fontFolders = array(
		'/usr/share/fonts/truetype/', // Linux (Ubuntu)
		'/usr/share/fonts/TTF/', // Linux (Arch)
		'/Library/Fonts/', // Mac OSX
		'/usr/X11/lib/X11/fonts/TTF/', // X11
		'c:/windows/fonts/', // Windows
	);

	/**
	 * @param string $text
	 * @param string|array $style "font-weight: bold; color: red" or array('color' => 'red', 'font-weight' => 'bold')
	 */
	function __construct($text, $style = array()) {
		$this->text = $text;
		$css = $this->parseStyle($style) + $this->parseStyle(self::$defaultStyle);
		foreach ($css as $rule => $value) {
			$property = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($rule))))); // Convert font-weight to fontWeigth
			$this->$property = $value;
		}
	}

	/**
	 * Render the text onto the given $gd resource
	 *
	 * @param resource $gd resource
	 * @param int $x
	 * @param int $y
	 */
	protected function rasterizeTo($gd, $x, $y) {
		$colorIndex = $this->colorIndex($this->color, $gd);
		if ($this->useBuildinFont()) {
			$font = $this->getBuildinFontIndex();
			$lines = explode("\n", $this->text); // Simuleer multiline textrendering
			foreach ($lines as $i => $text) {
				imagestring($gd, $font, $x, $y + ($i * $this->lineHeight * imagefontheight($font)), $text, $colorIndex);
			}
			return;
		}
		// Tekst m.b.v. een TTF font.
		$info = $this->resolveFont();
		if ($info) {
			$font = $info['filename'];
		} else {
			$font = implode(';', $this->fontFamily); // Font not found. trying GD internal font-finder.
		}
		imagefttext($gd, $this->fontSize, $this->angle, $x, $y + ceil($this->fontSize), $colorIndex, $font, $this->text, array('linespacing' => $this->lineHeight));
	}

	protected function rasterize() {
		$box = $this->getTextBounds();
		$width = $box['width'];
		$height = $box['height'];
		if ($this->gd !== null) {
			imagedestroy($this->gd);
			$this->gd = null;
		}
		$this->gd = $this->createCanvas($width, $height);
		$this->rasterizeTo($this->gd, 0, 0);
		return $this->gd;
	}

	/**
	 * Change the fontSize to fit the text inside the given width & height.
	 *
	 * @param int $width
	 * @param int $height
	 */
	function fitInto($width, $height) {
		// @todo implement beter font resize algoritm.
		for ($pt = 2; $pt < $height * 2; $pt += 0.5) {
			$this->fontSize = $pt;
			$box = $this->getTextBounds();
			if ($width < $box['width'] || $height < $box['height']) {
				$this->fontSize = $pt - 1;
				return;
			}
		}
	}

	protected function getWidth() {
		$box = $this->getTextBounds();
		return $box['width'];
	}

	protected function getHeight() {
		$box = $this->getTextBounds();
		return $box['height'];
	}

	/**
	 * Retrieve the bounds of the current text & style.
	 *
	 * @return array
	 *   array(
	 *     'width' => ?,
	 *     'height' => ?,
	 *     'top' => ?,
	 *     'left' => ?
	 *   );
	 */
	protected function getTextBounds() {
		if ($this->useBuildinFont() == false) {
			$info = $this->resolveFont();
			if ($info === false) {
				$font = $this->fontFamily;
			} else {
				$font = $info['filename'];
			}
			$box = imageftbbox($this->fontSize, $this->angle, $font, $this->text, array('linespacing' => $this->lineHeight));
			if ($box === false) {
				throw new \Exception('Unable to determine box for "'.$this->fontFamily.'"');
			}
			$result = array(
				'left' => ($box[0] < $box[6] ? $box[0] : $box[6]),
				'top' => ($box[5] < $box[7] ? $box[5] : $box[7]) + ceil($this->fontSize),
				'width' => ($box[2] > $box[4] ? $box[2] : $box[4]),
				'height' => ($box[1] > $box[3] ? $box[1] : $box[3]) + ceil($this->fontSize),
			);
			if ($result['left'] < 0) {
				$result['width'] += -1 * $result['left'];
			}
			if ($result['top'] < 0) {
				$result['height'] += -1 * $result['top'];
			}

			return $result;
		}
		// Using Buildin font metrics
		$font = $this->getBuildinFontIndex();
		$lines = explode("\n", $this->text);
		$height = (imagefontheight($font) * count($lines) * $this->lineHeight);
		$width = 0;
		foreach ($lines as $text) {
			$lineWidth = (imagefontwidth($font) * strlen($text)) - 1;
			if ($lineWidth > $width) {
				$width = $lineWidth;
			}
		}
		$topOffsets = array(
			1 => 1,
			2 => 3,
			3 => 2,
			4 => 3,
			5 => 3,
		);
		if ($font <= 5) {
			$top = $topOffsets[$font];
		} else {
			$top = 0;
		}
		return array(
			'left' => 0,
			'top' => $top,
			'width' => $width,
			'height' => $height,
		);
	}

	/**
	 * Doorzoek de fontmappen naar het opgegeven font.
	 *
	 * @return array|false  Geeft false als het font niet gevonden wordt.
	 */
	private function resolveFont($familyIndex = null) {
		if ($familyIndex === null) {
			// Loop all fontFamilies
			foreach ($this->fontFamily as $index => $font) {
				$info = $this->resolveFont($index);
				if ($info) {
					return $info;
				}
			}
			return false;
		}
		$id = strtolower($this->fontFamily[$familyIndex]);
		// Use DejaVu font as fallback
		switch ($id) {
			case 'sans-serif': $id = 'dejavu sans';
				break;
			case 'monospace': $id = 'dejavu sans mono';
				break;
			case 'serif': $id = 'dejavu serif';
				break;
		}
		$type = '';
		if ($this->fontWeight !== 'normal') {
			$type = ucfirst($this->fontWeight);
		}
		if ($this->fontStyle !== 'normal') {
			$type .= ' '.ucfirst($this->fontStyle);
		}
		$type = trim($type);
		if ($type !== '') {
			$id .= ' +'.strtolower($type);
		}
		$cacheFile = TMP_DIR.'TextGraphics_fontcache.ini';
		static $cache = null;
		if ($cache === null && file_exists($cacheFile)) {
			$cache = parse_ini_file($cacheFile, true);
		}
		if (isset($cache[$id])) {
			if ($cache[$id] === 'NOT_FOUND') {
				notice('(Cached)Font: "'.trim($this->fontFamily[$familyIndex].' '.$type).'" not found');
				return false;
			}
			if (file_exists($cache[$id]['filename'])) {
				return $cache[$id];
			} else {
				// Cache file contains invalid data
				file_put_contents($cacheFile, '; Fonts');
				$cache = array();
			}
		}
		$fontFolders = array_merge(array(
			dirname(dirname(__FILE__)).'/fonts/', // Module fonts folder containing "Bitstream Vera"
				), self::$fontFolders);

		// Add 1 level subfolders
		foreach ($fontFolders as $folder) {
			if (is_dir($folder)) {
				$dir = new \DirectoryIterator($folder);
				foreach ($dir as $entry) {
					if ($entry->isDot() == false && $entry->isDir()) {
						$fontFolders[] = $entry->getPathname().'/';
					}
				}
			}
		}
		$fonts = array();
		foreach ($fontFolders as $folder) {
			if (is_dir($folder) == false) {
				continue;
			}
			// Het font heeft een andere bestandnaam (file_exists is hoofdlettergevoelig)
			$dir = new \DirectoryIterator($folder);
			foreach ($dir as $entry) {
				$filename = $entry->getFilename();
				if (substr($filename, 0, 1) == '.') {
					// directories en verborgen bestanden overslaan
					continue;
				}
				if (in_array(strtolower(file_extension($filename)), array('ttf', 'otf')) == false) {
					// Only parse *.ttf en *.otf files.
					continue;
				}
				$ttf = new TrueTypeFont($entry->getPathname());
				$properties = $ttf->getNameTable();
				unset($ttf);
				if (!isset($properties['1::0::0'][2])) {
					continue;
				}
				$info = array(
					'name' => (isset($properties['1::0::0'][16]) ? $properties['1::0::0'][16] : $properties['1::0::0'][1]),
					'type' => $properties['1::0::0'][2],
					'filename' => $entry->getPathname(),
				);
				$fonts[] = $info['name'];
				if (in_array($info['type'], array('Regular', 'Roman', 'Book'))) {
					$alias = strtolower($info['name']);
				} elseif ($type === $info['type']) {
					$alias = strtolower($info['name']).' +'.strtolower($info['type']);
				} else {
					continue; // Incorrect type (Bold !== Italic)
				}
				if ($id === $alias) {
					$cache[$id] = $info;
					write_ini_file($cacheFile, $cache, 'Fonts');
					return $info;
				}
			}
		}
		notice('Font: "'.trim($this->fontFamily[$familyIndex].' '.$type).'" not found', array('Available fonts' => quoted_implode(', ', array_unique($fonts))));
		$cache[$id] = 'NOT_FOUND';
		write_ini_file($cacheFile, $cache, 'Fonts');
		return false;
	}

	/**
	 * Geeft aan of er gebruik gemaakt moet worden van de build-in fonts.
	 *
	 * @return bool
	 */
	private function useBuildinFont() {
		if (is_int($this->fontFamily)) {
			return true;
		}
		$capabilities = gd_info();
		return ($capabilities['FreeType Support'] == false);
	}

	/**
	 * Geeft het interne font o.p.v. de $this->size
	 * Als $this->font een integer is, wordt deze gebruikt.
	 *
	 * @return int
	 */
	private function getBuildinFontIndex() {
		if (is_int($this->fontFamily)) { // Is er een specifieke buildin font geselecteerd?
			return $this->fontFamily;
		}
		// Voor de grootte (size) is uitgegaan van vergelijkingen met het "Bitstream Vera Sans" font.
		if ($this->fontSize <= 9) {
			return 1; // grootte is ca 7px
		} elseif ($this->fontSize > 9 && $this->fontSize <= 12) { // 10 t/m 12
			return ($this->fontWeight ? 3 : 2); // grootte is ca 11px
		} else { // 13px and up
			return ($this->fontWeight ? 5 : 4); // grootte is 12 a 13px
		}
	}

	/**
	 * Parse css rules into fontOptions
	 *
	 * @param string|array $style
	 * @return array
	 */
	private function parseStyle($style) {
		if (is_array($style)) {
			$rules = $style;
		} else {
			$lines = explode(';', $style);
			$rules = array();
			foreach ($lines as $line) {
				$line = trim(str_replace(array("\n", "\r"), ' ', $line));
				if (preg_match('/^(?P<name>[a-z0-9-]+)[\s]{0,}:[\s]{0,}(?P<value>.+)$/', $line, $matches)) {
					$rules[$matches['name']] = $matches['value'];
				} elseif ($line !== '') {
					notice('Failed parsing line: "'.$line.'"');
				}
			}
		}
		$css = array();
		foreach ($rules as $rule => $value) {
			$rule = strtolower($rule);
			$value = trim(str_replace(array("\n", "\r"), ' ', $value));
			switch ($rule) {

				case 'font':
					if (preg_match('/^(((?P<weight>bold)|(?P<style>italic)|(?P<size>[0-9]+(px|pt|%))(\/(?P<height>[0-9\.]+(px|pt|%){0,1})))[ ]*)*(?P<family>.+)$/i', $value, $matches)) {
						if ($matches['weight']) {
							$css['font-weight'] = $this->parseRule('font-weight', $matches['weight']);
						}
						if ($matches['style']) {
							$css['font-style'] = $this->parseRule('font-style', $matches['style']);
						}
						if ($matches['size']) {
							$css['font-size'] = $this->parseRule('font-size', $matches['size']);
						}
						if ($matches['height']) {
							$css['line-height'] = $this->parseRule('line-height', $matches['height']);
						}
						if ($matches['family']) {
							$css['font-family'] = $this->parseRule('font-family', $matches['family']);
						}
					} else {
						notice('Failed parsing font: "'.$value.'" failed');
					}
					break;

				default:

					$css[$rule] = $this->parseRule($rule, $value);
			}
		}
		return $css;
	}

	/**
	 * Validates and corrects css rule values.
	 *
	 * @param string $property CSS property. "font-weight", etc
	 * @param string $value Example: "bold"
	 * @return string
	 */
	private function parseRule($property, $value) {
		switch ($property) {

			case 'font-weight':
				$value = strtolower($value);
				if (in_array($value, array('bold', 'normal')) == false) {
					notice('Unsupported value: "'.$value.'" for css property: "'.$property.'"');
				}
				return $value;

			case 'font-style':
				$value = strtolower($value);
				if (in_array($value, array('italic', 'oblique', 'normal')) == false) {
					notice('Unsupported value: "'.$value.'" for css property: "'.$property.'"');
				}
				return $value;

			case 'font-size':
				if (preg_match('/^([0-9]+)px$/', $value, $match)) {
					return (float) ($match[1] * 0.75);
				}
				if (preg_match('/^([0-9]+)pt$/', $value, $match)) {
					return (float) ($match[1]);
				}
				notice('Only font-sizes in pt are supported');
				return $value;

			case 'line-height':
				if (preg_match('/^([0-9\.]+)%$/', $value, $match)) {
					return ($match[1] / 100.0);
				}
				if (preg_match('/^([0-9\.]+)$/', $value, $match)) {
					return (float) ($match[1]);
				}
				notice('Only line-heights as factor are supported');
				return $value;

			case 'font-family':
				$fonts = preg_split('/,[\s]*/', $value);
				foreach ($fonts as $index => $font) {
					$fonts[$index] = str_replace(array('"', "'"), '', $font);
				}
				return $fonts;

			default:
				notice('CSS property: "'.$property.'" not supported');
				return $value;
		}
	}

}

?>
