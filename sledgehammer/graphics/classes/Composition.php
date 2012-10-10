<?php
/**
 * Composition
 */
namespace Sledgehammer;
/**
 * A composition with layers of Graphics objects, which acts as a single graphics object.
 * Allows for a treestructure of Graphics layer's, like photoshop folders.
 *
 * @package Graphics
 */
class Composition extends Graphics {

	/**
	 * @var array|Graphics Array containing Graphics objects & coordinates
	 */
	protected $layers;

	/**
	 * Create a Composition object.
	 *
	 * When initialized with a background layer, the composition is automaticly clipped at the dimensions of that layer.
	 *
	 * Usages:
	 *  new Composition(200, 150); // Create 200 x 150 transparent canvas as background.
	 *  new Composition(200, 150, 'black'); // Create 200 x 150 black canvas as background.
	 *  new Composition('/tmp/upload.jpg'); // Use an image as background.
	 *  new Composition(new Image('/path/to/file')); // Use a Graphics object as background
	 *  new Composition(array('graphics' => new TextLayer('Hi'), 'position' => array('y' => 0, 'y' => 0))); // Use the array as $this->layers
	 *
	 * @param $mixed
	 */
	function __construct($mixed = array()) {
		if (is_array($mixed)) {
			$this->layers = $mixed;
			return;
		}
		if (is_numeric($mixed) && func_num_args() >= 2) {
			if (func_num_args() === 2) {
				$layer = new Canvas($mixed, func_get_arg(1));
			} else { // with bgcolor
				$layer = new Canvas($mixed, func_get_arg(1), func_get_arg(2));
			}
		} elseif (is_string($mixed)) {
			$layer = new Image($mixed);
		} elseif (is_object($mixed) && $mixed instanceof Graphics) {
			$layer = $mixed;
		} else {
			throw new InfoException('Argument 1 is invalid, expecting a filename, GraphicsLayer or dimentions', $mixed);
		}
		$this->layers = array(
			'background' => array(
				'graphics' => $layer,
				'position' => array(
					'x' => 0,
					'y' => 0
				)
			)
		);
		// Clip the composition bases on the layer dimensions
		$this->width = $layer->width;
		$this->height = $layer->height;
	}

	/**
	 * Add a layer on top of the other layers.
	 *
	 * @param Graphics $graphics
	 * @param array $position array(
	 * 	 'x'  int Left position
	 *   'y'  int Top position
	 * )
	 * @param string $name (optional) unique key for the layer
	 */
	function add($graphics, $position, $name = null) {
		if ($name === null) {
			array_unshift($this->layers, array(
				'position' => $position,
				'graphics' => $graphics,
			));
			return;
		}
		if (array_key_exists($name, $this->layers)) {
			notice('Removing existing layer: "'.$name.'"');
			unset($this->layers[$name]);
		}
		$layers = array_reverse($this->layers);
		$layers[$name] = array(
			'position' => $position,
			'graphics' => $graphics,
		);
		$this->layers = array_reverse($layers);
	}

	function getLayer($name) {
		return $this->layers[$name];
	}

	/**
	 * Allow setting the width and height properties
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	function __set($property, $value) {
		if ($property === 'width' || $property === 'height') {
			$this->$property = $value;
		} else {
			parent::__set($property, $value);
		}
	}

	/**
	 * When no width is set, calculate the width
	 *
	 * @return int
	 */
	function getWidth() {
		if ($this->gd !== null) {
			return imagesx($this->gd);
		}
		$maxWidth = 0;
		foreach ($this->layers as $layer) {
			$width = $layer['position']['x'] + $layer['graphics']->width;
			if ($width > $maxWidth) {
				$maxWidth = $width;
			}
		}
		return $maxWidth;
	}

	/**
	 * When no height is set, calculate the height
	 *
	 * @return int
	 */
	function getHeight() {
		if ($this->gd !== null) {
			return imagesy($this->gd);
		}
		$maxHeight = 0;
		foreach ($this->layers as $layer) {
			$height = $layer['position']['y'] + $layer['graphics']->height;
			if ($height > $maxHeight) {
				$maxHeight = $height;
			}
		}
		return $maxHeight;
	}

	protected function rasterize() {
		if ($this->gd !== null) {
			imagedestroy($this->gd); // Free memory
			$this->gd = null;
		}
		$count = count($this->layers);
		if ($count === 0) {
			return parent::rasterize(); // return a nixel
		}
		$width = $this->width;
		$height = $this->height;
		if ($count === 1) {
			reset($this->layers);
			$layer = current($this->layers);
			if ($layer['position']['x'] == 0 && $layer['position']['y'] == 0 && $width === $layer['graphics']->width && $height === $layer['graphics']->height) {
				// The container only contains 1 layer.
				return $layer['graphics']->rasterize();
			}
		}
		$this->gd = $this->createCanvas($width, $height);
		foreach (array_reverse($this->layers) as $layer) {
			$layer['graphics']->rasterizeTo($this->gd, $layer['position']['x'], $layer['position']['y']);
		}
		return $this->gd;
	}

}

?>
