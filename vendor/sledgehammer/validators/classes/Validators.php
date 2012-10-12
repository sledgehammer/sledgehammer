<?php
namespace Sledgehammer;
/**
 * Een validator die via meerdere Validator objecten de waarde controleert
 *
 * @package Validators
 */
class Validators extends Object implements Validator {

	public $Validators; // Array met validator objecten

	function __construct($Validators = array()) {
		$this->Validators = $Validators;
	}

	function validate($value, &$error_message) { // [bool]
		if (!is_array($this->Validators)) {
			$error_message = 'Unexpected '.gettype($this->Validators).', expecting array';
			notice($error_message);
			return false;
		}
		foreach ($this->Validators as $Validator) {
			if (!$Validator->validate($value, $error_message)) {
				return false;
			}
		}
		return true;
	}

}

?>
