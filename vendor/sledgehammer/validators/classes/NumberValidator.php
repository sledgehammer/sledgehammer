<?php
namespace Sledgehammer;
/**
 * Controleert of de waarde een getal is.
 * Ook is het mogenlijk om te controleren of het om een geheel getal gaat en/of de waarde binnen een bereik valt
 *
 * @package Validators
 */
class NumberValidator extends Object implements Validator {

	/**
	 * @var  NULL|number Minimum toegestane waarde
	 */
	private $minimum;

	/**
	 * @var NULL|number Maximum toegestane waarde
	 */
	private $maximum;

	/**
	 * @var bool true: alleen gehele getallen toestaan
	 */
	private $integers_only = false;

	/**
	 * @param array $options array('integers_only' => bool, 'minimum' => number, 'maximum' => number)
	 */
	function __construct($options = array()) {
		foreach ($options as $option => $value) {
			switch ($option) {

				case 'integers_only':
					$this->integers_only = $value;
					break;

				case 'minimum':
					$this->minimum = $value;
					break;

				case 'maximum':
					$this->maximum = $value;
					break;

				default:
					warning('Unexpected option: "'.$option.'", value: "'.$value.'"');
					break;
			}
		}
	}

	function validate($value, &$error_message) {
		if (is_numeric($value)) {
			if ($this->integers_only && fmod($value, 1) !== 0.0) {
				$error_message = 'Only integers are allowed';
				return false;
			}
			if ($this->minimum !== NULL && $value < $this->minimum) {
				$error_message = 'Minimum: '.$this->minimum;
				return false;
			}
			if ($this->maximum !== NULL && $value > $this->maximum) {
				$error_message = 'Maximum: '.$this->maximum;
				return false;
			}
			return true;
		} else {
			$error_message = 'Not a number';
			return false;
		}
	}

}

?>
