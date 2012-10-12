<?php
/**
 * HtmlFilter converts raw text into htmlencoded text.
 * Protects against XSS attacks
 *
 * @todo Implement a tag whitelist.
 *
 * @package Filter
 */
namespace Sledgehammer;

class HtmlFilter extends Object implements Filter {

	/**
	 * Deze filter kijkt naar het datatype en geeft een html-safe waarde terug.
	 * In tegenstelling tot de toHtml filter worden booleans e.d. niet omgezet naar een string.
	 *
	 * @param mixed $text
	 * @return mixed xss-safe value
	 */
	function filter($text) {
		switch (gettype($text)) {

			case 'string':
				return htmlentities($text, ENT_COMPAT, Framework::$charset);

			case 'NULL':
			case 'boolean':
			case 'integer':
			case 'double':
				return $text; // Deze types kunnen geen xss tags bevatten.

			case 'object':
				if (method_exists($text, '__toString')) { // Kan het object omgezet worden naar een string?
					return htmlentities($text, ENT_COMPAT, Framework::$charset); // Omzet naar string en deze string escapen.
				}
				notice('Objects without __toString() implementation are not allowed');
				return null;

			default:
			case 'resource':
			case 'array':
				notice('Unacceptable type: "'.gettype($text).'"');
				return null;
		}
	}
}

?>
