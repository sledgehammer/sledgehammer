<?php
/**
 * Global functions
 * @package Validators
 */
namespace Sledgehammer;

/**
 * Shortcut to use a Validator object.
 *   $validator = new ValidatorClass();
 *   $is_valid = $validator->validate($value, $error_message);
 * Becomes
 *   $is_valid = validate($value, $error_message, new ValidatorClass());
 *
 * @param mixed $value
 * @param string $$error
 * @param Validator $validator
 * @return bool
 */
function validate($value, &$error, $validator) {
	if (method_exists($validator, 'validate') === false) {
		throw new \Exception('The given "validator" is not compatible with the Validator interface');
	}
	return $validator->validate($value, $error);
}

?>
