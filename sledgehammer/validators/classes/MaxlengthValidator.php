<?php
/**
 * MaxlengthValidator
 * @package Validators
 */
namespace Sledgehammer;
/**
 * Controleert of het aantal karakters niet te groter is dan de opgegeven lengte.
 */
class MaxlengthValidator extends Object implements Validator {

	private $max;

	/**
	 *
	 * @param int $maximum
	 */
	function __construct($maximum) {
		$this->max = $maximum;
	}

	function validate($value, &$error_message) { // [bool]
		if (strlen($value) <= $this->max) {
			return true;
		} else {
			$error_message = 'Maximum length of '.$this->max.' exceeded by '.(strlen($value) - $this->max);
			return false;
		}
	}

}

?>
