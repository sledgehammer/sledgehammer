<?php
/**
 * Import
 */
namespace Sledgehammer;
/**
 * Iterface for importing request data.
 *
 * @example
 * $data = $import->import($error);
 * if ($error) {
 *   // report error
 * } else {
 *  // do something
 * }
 *
 * @package MVC
 */
interface Import {

	/**
	 * Set the (default) value.
	 *
	 * @return void
	 */
	function initial($value);

	/**
	 * Returns the imported value.
	 *
	 * @param mixed $error
	 * @param mixed $request
	 * @return mixed
	 */
	function import(&$error, $request = null);
}

?>
