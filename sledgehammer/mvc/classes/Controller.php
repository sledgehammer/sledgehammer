<?php
/**
 * Controller
 */
namespace Sledgehammer;
/**
 * Interface for Controllers, the C in MVC.
 *
 * @package MVC
 */
interface Controller {

	/**
	 * Build and return a view object
	 *
	 * @return View
	 */
	function generateContent();

}
?>
