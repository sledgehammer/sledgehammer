<?php
namespace Sledgehammer;
/**
 * Voorkomt cross site scripting (XSS)
 * Beschermt tegen javascript en html injecties door "<" en ">" niet toe te staan
 *
 * @package Validators
 */
class XSSValidator extends Object implements Validator {

	function validate($value, &$error_message) { // [bool]
		if (strpos($value, '<') !== false) {
			$error_message = 'Character "<" is not allowed';
			return false;
		} elseif (strpos($value, '>') !== false) {
			$error_message = 'Character ">" is not allowed';
			return false;
		}
		return true;
	}

}

?>
