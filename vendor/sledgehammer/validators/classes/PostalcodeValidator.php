<?php
namespace Sledgehammer;
/**
 * Controleerd of de postcode voldoet aan de nederlandse postcode notatie
 *
 * @package Validators
 */
class PostalcodeValidator extends Object implements Validator {

	function validate($value, &$error_message) {
		if (preg('/^[0-9]{4}[A-Z]{2}/', $value)) { // Nederlandse postcode notatie
			return true;
		} else {
			$error_message = 'Invalid postalcode';
			return false;
		}
	}

}

?>
