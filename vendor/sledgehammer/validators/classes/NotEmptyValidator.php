<?php
namespace Sledgehammer;
/**
 * Controleert of er een waarde is ingesteld.
 *
 * @package Validators
 */
class NotEmptyValidator extends Object implements Validator {

	function validate($value, &$error_message) {
		if (trim($value) != '') {
			return true;
		} else {
			$error_message = 'Value shouldn\'t be empty';
			return false;
		}
	}

}

?>
