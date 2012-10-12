<?php
/**
 * Input
 */
namespace Sledgehammer;
/**
 * An <input> element.
 *
 * @package MVC
 */
class Input extends Object implements View, Import {

	/**
	 * Input name.
	 * @var string
	 */
	public $name;

	/**
	 * Input type. 'text', 'checkbox', 'radio', 'select', 'textarea'
	 * @var string
	 */
	protected $type = 'text';
	protected $value;

	/**
	 * Attributes for the input element.
	 * @var array
	 */
	protected $attributes = array();

	protected $label;

	function __construct($options) {
		// Set attributes and properties
		foreach ($options as $option => $value) {
			if (property_exists($this, $option)) {
				$this->$option = $value;
			} else {
				$this->attributes[$option] = $value;
			}
		}
		$this->type = strtolower($this->type);
	}

	function initial($value) {
		$this->value = $value;
	}

	function import(&$error, $request = null) {
		if ($request === null) {
			$request = $_REQUEST;
		}
		switch ($this->type) {

			case 'file':
				// Import a file upload
				if (count($_FILES) == 0) {
//					if (!array_key_exists('_FILES', $request)) {
//						return null; // Het formulier is nog niet gepost
//					}
					notice('$_FILES is empty, check for <form enctype="multipart/form-data">');
					return null;
				}
				if (array_key_exists($this->name, $_FILES) == false) {
					$error = 'Invalid name';
					return null;
				}
				$file = $_FILES[$this->name]; // @todo support for multiple files
				switch ($file['error']) {

					case UPLOAD_ERR_OK:
						unset($file['error']);
						return $file;

					case UPLOAD_ERR_NO_FILE:
						// @todo Check if the input was required.
						break;

					case UPLOAD_ERR_INI_SIZE:
						$error = 'De grootte van het bestand is groter dan de in php.ini ingestelde waarde voor upload_max_filesize';
						break;

					case UPLOAD_ERR_FORM_SIZE:
						$error = 'De grootte van het bestand is groter dan de in html gegeven MAX_FILE_SIZE';
						break;

					case UPLOAD_ERR_PARTIAL:
						$error = "Het bestand is maar gedeeltelijk geupload";
						break;

					default:
						$error = 'Unknown error: "'.$file['error'].'"';
				}
				return null; // Er is geen (volledig) bestand ge-upload

			default:
				if ($this->name === null) {
					return null; // De naam is niet opgegeven.
				}
				if (extract_element($request, $this->name, $value)) {
					$this->value = $value;
					return $value;
				}

				$error = 'Import failed';
				return null;
		}
	}

	function render() {
		if ($this->label === null) {
			$this->renderElement();
		} else {
			if (in_array($this->type, array('checkbox', 'radio'))) {
				echo '<label>';
				$this->renderElement();
				echo '&nbsp;', HTML::escape($this->label), '</label>';
			} else {
				echo '<label>', HTML::escape($this->label), '</label>';
				$this->renderElement();
			}
			return;
		}
	}

	protected function renderElement() {
		$attributes = $this->attributes;
		if ($this->name !== null) {
			array_key_unshift($attributes, 'name', $this->name);
		}
		switch ($this->type) {
			case 'select':
				$options = $attributes['options'];
				unset($attributes['options']);
				echo HTML::element('select', $attributes, true);
				$isIndexed = is_indexed($options);
				foreach ($options as $value => $label) {
					$option = array();
					if ($isIndexed) {
						$value = $label;
					} else {
						$option['value'] = $value;
					}
					if (equals($value, $this->value)) {
						$option['selected'] = 'selected';
					}
					echo HTML::element('option', $option, HTML::escape($label));
				}
				echo '</select>';
				break;

			case 'textarea':
				echo HTML::element('textarea', $attributes, HTML::escape($this->value));
				break;

			default:
				array_key_unshift($attributes, 'type', $this->type);
				if ($this->value !== null) {
					$attributes['value'] = $this->value;
				}
				echo HTML::element('input', $attributes);
				break;
		}
	}

	function __toString() {
		return view_to_string($this);
	}

}

?>
