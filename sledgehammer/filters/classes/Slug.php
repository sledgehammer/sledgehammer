<?php
/**
 * Slug
 * @package Filters
 */
namespace Sledgehammer;
/**
 * Veranderd een willekeurige string naar een nette bestandsnaam
 * Corrigeerd karakers zoals "o-met puntjes"naar een "o"
 */
class Slug extends Object implements Filter {

	private $lowercase = true;
	private $charset = null;

	function __construct($options) {
		foreach ($options as $property => $value) {
			$this->$property = $value;
		}

	}

	public function filter($value)	{
		$value = self::convertSpecialChars($value, $this->charset);

		$value = preg_replace('/[^a-z0-9\.]/i', '-', $value);
		$value = preg_replace('/[\-]{2,}/', '-', $value);
		$value = preg_replace('/[\.]{2,}/', '.', $value);
		if ($this->lowercase) {
			$value = strtolower($value);
		}
		$value = trim($value,'-');
		$value = rtrim($value,'.');
		return $value;
	}

	/**
	 * @param string $text line of encoded text
	 * @param string $from_enc (encoding type of $text, e.g. UTF-8, ISO-8859-1)
	 *
	 * @return 7bit representation
	 */
	static function convertSpecialChars($text, $charset = null) {
		if ($charset === null) {
			$charset = Framework::$charset;
		}
    	//$text = mb_convert_encoding($text,'HTML-ENTITIES',$charset);
    	$text = htmlentities($text, ENT_COMPAT, $charset).'"';
    	$text = preg_replace(
			array('/&szlig;/','/&(..)lig;/',
			'/&([aouAOU])uml;/','/&(.)[^;]*;/'),
			array('ss',"$1","$1".'e',"$1"),
			$text
		);
    	return $text;
	}

	/**
	 * @author Tim Schipper
	 */
	static function convertSpecialChars2($string) {
		$string = strtr($string,
					"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñÐÝýÞßðþ",
					"AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNnDYyPbop");
		$string = str_replace(array('Æ','æ'),array('AE','ae'),$string);
		return $string;
	}
}
?>