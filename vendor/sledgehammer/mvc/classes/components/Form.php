<?php
/**
 * Form
 */
namespace Sledgehammer;
/**
 * Generate and import a Form
 * @package MVC
 */
class Form extends Object implements View, Import {

	/**
	 * @var string
	 */
	protected $legend;

	/**
	 * @var array
	 */
	protected $fields = array();

	/**
	 * @var array
	 */
	protected $actions = array();

	/**
	 * @var bool
	 */
	protected $fieldset = true;

	/**
	 * @var array
	 */
	private $attributes = array(
		'method' => 'post'
	);

	/**
	 * Constructor
	 * @param array $options
	 */
	function __construct($options = array()) {
		// Set attributes and properties
		foreach ($options as $option => $value) {
			if (property_exists($this, $option)) {
				$this->$option = $value;
			} else {
				$this->attributes[$option] = $value;
			}
		}
	}

	function initial($values) {
		foreach ($values as $field => $value) {
			$this->fields[$field]->initial($value);
		}
	}

	function import(&$error, $request = null) {
		if ($request === null) {
			if (strtolower($this->attributes['method']) === 'post') {
				$request = $_POST;
			} elseif (strtolower($this->attributes['method']) === 'get') {
				$request = $_GET;
			} else {
				notice('Invalid import method');
				$request = $_REQUEST;
			}
		}
		if (count($request) == 0) {
			$error = false;
			return null;
		}
		$data = array();
		foreach ($this->fields as $key => $field) {
			$data[$field->name] = $field->import($fieldError, $request);
			if ($fieldError) {
				$error[$field->name] = $fieldError;
			}
		}
		if (count($error)) {
			return null;
		}
		return $data;
	}

	function render() {
		echo HTML::element('form', $this->attributes, true), "\n";
		$this->renderContents();
		echo '</form>';
	}

	function renderContents() {
		if (array_value($this->attributes, 'class') === 'form-horizontal') {
			$renderControlGroups = true;
		} else {
			$renderControlGroups = false;
		}
		if ($this->fieldset) {
			echo "<fieldset>\n";
			if ($this->legend !== null) {
				echo "\t<legend>", HTML::escape($this->legend), "</legend>\n";
			}
		}

		// Render form fields
		foreach ($this->fields as $label => $field) {
			echo "\t";
			if ($renderControlGroups) {
				echo '<div class="control-group">';
				if (is_int($label) === false) {
					echo '<label class="control-label">', HTML::escape($label), '</label>';
				}
				echo '<div class="controls">';
				render($field);
				echo "</div></div>";
			} else {
				if (is_int($label) === false) {
					echo '<label>', HTML::escape($label), '</label>';
				}
				render($field);
			}
			echo "\n";
		}

		// Render form actions
		if (count($this->actions) !== 0) {
			echo '<div class="form-actions">';
			foreach ($this->actions as $name => $action) {
				if (is_string($name)) {
					if (is_array($action) === false) {
						$action = array(
							'label' => $action
						);
					}
					$action['name'] = $name;
				}
				echo new Button($action);
			}
			echo '</div>';
		}
		if ($this->fieldset) {
			echo "</fieldset>\n";
		}
	}

}

?>
