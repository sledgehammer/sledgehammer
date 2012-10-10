<?php
/**
 * Filter using an existing function
 *
 * @package Filters
 */
namespace Sledgehammer;
class FunctionFilter extends Object implements Filter {

	private
		$callback,
		$default_parameters,
		$parameter_index; 

	/**
	 *
	 * @param string|array $callback Naam van de functie of een array($Object, 'methode')
	 * @param array $default_parameters Zodat je ook functie kunt gebruiken die meerdere parameters hebben. Zoals bv date()
	 * @param int $parameter_index De index van de parameter die door de filter vervangen zal worden. Als je de filter het 2de argument moet vervangen zet je de $paramer_index op 1
	 */
	function __construct($callback, $default_parameters = array(), $parameter_index = 0) {
		if (!is_callable($callback)) {
			throw new \Exception('$callback isn\'t callable');
		}
		$this->callback = $callback;
		$this->default_parameters = $default_parameters;
		$this->parameter_index = $parameter_index;
	}

	/**
	 * De waarde filteren met behulp van de opgegeven functie
	 */
	function filter($value) {
		$parameters = $this->default_parameters;
		$parameters[$this->parameter_index] = $value;
		return call_user_func_array($this->callback, $parameters);
	}
}
?>
