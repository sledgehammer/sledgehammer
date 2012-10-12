<?php
/**
 * KeyExistsValidator
 * @package Validators
 */
namespace Sledgehammer;
/**
 * Controleert of de waarde voorkomt als key in de array.
 */
class KeyExistsValidator extends Object implements Validator {

	private $array;

	function __construct($array = array()) {
		$this->array = $array;
	}

	function validate($value, &$error_message) { // [bool]
		if (array_key_exists($value, $this->array)) {
			return true;
		} else {
			$error_message = 'Invalid option';
			return false;
		}
	}

}

?>
