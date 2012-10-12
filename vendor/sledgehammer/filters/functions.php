<?php
/**
 * Global functions
 * @package Filters
 */
namespace Sledgehammer;
/**
 * Shorthand for using use a Filter object.
 *   $Filter = new FilterClass;
 *   $filtered_value = $Filter->filter($value);
 * Becomes
 *   $filtered_value = filter($value, new FilterClass);
 *
 * @param mixed $value Input for the Filter
 * @param Filter $Filter a Filter object
 * @return mixed filtered output
 */
function filter($value, $filter) {
	if (method_exists($filter, 'filter') === false) {
		throw new InfoException('The $filter parameter doesn\'t have a filter() method', $filter);
	}
	return $filter->filter($value);
}

?>
