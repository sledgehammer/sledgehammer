<?php
/**
 * EmailValidator
 * @package Validators
 */
namespace Sledgehammer;
/**
 * Checks if the email is a valid email address
 * Can check if the domain exists in the dns.
 */
class EmailValidator extends Object implements Validator {

	private $check_dns;

	function __construct($check_dns = false) {
		$this->check_dns = $check_dns;
	}

	function validate($email, &$error) {
		$atIndex = strrpos($email, '@');

		if (is_bool($atIndex) && !$atIndex) {
			$error = "This not an email address";
			return false;
		}
		$domain = substr($email, $atIndex + 1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);

		if ($localLen < 1 || $localLen > 64) {
			$error = 'The local part of the email address length exceeded';
			return false;
		}
		if ($domainLen < 1 || $domainLen > 255) {
			$error = 'The domain part of the email address length exceeded';
			return false;
		}
		if ($local[0] == '.' || $local[$localLen - 1] == '.') {
			$error = "The local part of the email address starts or ends with '.'";
			return false;
		}
		if (preg_match('/\\.\\./', $local)) {
			$error = "The local part of the email address has two consecutive dots.";
			return false;
		}
		if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
			$error = "character not valid in domain part of the email address.";
			return false;
		}
		if (preg_match('/\\.\\./', $domain)) {
			$error = "The domain part of the email address has two consecutive dots.";
			return false;
		}
		if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
			$error = "character not valid in local part of the email address unless local part of the email address is quoted";
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
				return false;
			}
		}
		if ($this->check_dns && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
			$error = "The domain not found in DNS";
			return false;
		}
		// The emailadress is valid
		return true;
	}

}

?>
