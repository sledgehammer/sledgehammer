<?php
/**
 * Button
 */
namespace Sledgehammer;
/**
 * A button (btn) element.
 *
 * @package MVC
 */
class Button extends Object implements View {

	/**
	 * @var string
	 */
	protected $icon;

	/**
	 * @var string
	 */
	protected $label;



	/**
	 * @var string Determine the element type (a|button|input)
	 */
	protected $element = 'button';

	/**
	 * Attributes for the html element.
	 * @var array
	 */
	protected $attributes = array(
		'class' => 'btn'
	);

	/**
	 * Contructor
	 * @param string|array $label_or_options
	 * @param array $options
	 */
	function __construct($label_or_options, $options = array()) {
		if (is_array($label_or_options) === false) {
			$options['label'] = $label_or_options;
		} else {
			if (count($options) !== 0) {
				notice('Second parameter $options is ignored');
			}
			$options = $label_or_options;
		}
		// Set attributes and properties
		foreach ($options as $option => $value) {
			if (property_exists($this, $option)) {
				$this->$option = $value;
			} else {
				$this->attributes[$option] = $value;
			}
		}
	}

	function render() {
		if ($this->icon) {
			$label = HTML::icon($this->icon).'&nbsp;'.HTML::escape($this->label);
		} else {
			$label = HTML::escape($this->label);
		}
		echo HTML::element($this->element, $this->attributes, $label);
	}

	function __toString() {
		return view_to_string($this);
	}

}

?>
