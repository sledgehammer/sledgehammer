<?php
/**
 * FilterWrapper
 * @package Filters
 */
namespace Sledgehammer;
/**
 * Wraps all properties/elements with a filter.
 */
class FilterWrapper extends Wrapper {

	/**
	 * @var Filter
	 */
	protected $_filter;
	protected $_filterIn;
	protected $_filterOut;

	function __construct($data, $options = array()) {
		parent::__construct($data, $options);
		if ($this->_filter === null) {
			throw new \Exception('option "filter" is required for a FilterObject');
		}
		if (method_exists($this->_filter, 'filter') === false) {
			throw new \Exception('The given "filter" is not compatible with the Filter interface');
		}
		if ($this->_filterIn === null) {
			$this->_filterIn = $this->_filter;
		}
		if ($this->_filterOut === null) {
			$this->_filterOut = $this->_filter;
		}
	}

	protected function out($value, $element, $context) {
		$value = parent::in($value, $element, $context);
		return $this->_filterOut->filter($value);
	}

	protected function in($value, $element, $context) {
		$value = parent::in($value, $element, $context);
		dump($this);
		dump($element);
		return $this->_filterIn->filter($value);
	}
}

?>
