<?php
/**
 * ViewHeaders
 */
namespace Sledgehammer;
/**
 * Add Headers to any View.
 * Makes it possible to add headers from a Controller without adding a headers to all View classes.
 *
 * @package MVC
 */
class ViewHeaders extends Object implements View {

	/**
	 * @var View
	 */
	private $view;
	/**
	 * @var array
	 */
	private $headers;
	/**
	 * @var bool
	 */
	private $overrideHeaders;

	/**
	 *
	 * @param View $view
	 * @param array $headers
	 * @param bool $overrideHeaders Bij false zullen de headers van het component leidend zijn.
	 */
	function  __construct($view, $headers, $overrideHeaders = false) {
		$this->view = $view;
		$this->headers = $headers;
	}

	function getHeaders() {
		if ($this->overrideHeaders == false) {
			return merge_headers($this->headers, $this->view); //  standaard merge
		}
		// De headers van dit object zijn leidend.
		if (method_exists($this->view, 'getHeaders')) {
			return merge_headers($this->view->getHeaders(), $this->headers);
		}
		return $this->headers; // Het component had geen headers.
	}

	function render() {
		if (is_valid_view($this->view)) {
			$this->view->render();
		}
	}

	function isDocument() {
		if (method_exists($this->view, 'isDocument'))
		{
			return $this->view->isDocument();
		}
		return false;
	}
}
?>
