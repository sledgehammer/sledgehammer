<?php
namespace Sledgehammer;
/**
 * Door middel van een reguliere expressie de waarde controleren
 *
 * @package Validators
 */
class RegexValidator extends Object implements Validator {

	private $pattern;
	private $human_readable_description;

	/**
	 * @param string $pattern De reguliere expressie
	 * @param string $human_readable_description Korte beschijving van het pattroon zoals "Date-format yyy-mm-dd", "emailaddress", "postalcode", etc
	 */
	function __construct($pattern, $human_readable_description) {
		$this->pattern = $pattern;
		$this->human_readable_description = $human_readable_description;
	}

	function validate($value, &$error_message) {
		if (preg_match($this->pattern, $value)) {
			return true;
		} else {
			$error_message = 'Invalid format, expecting "'.$this->human_readable_description.'"';
			return false;
		}
	}

}

?>
