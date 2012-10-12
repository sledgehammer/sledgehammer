<?php
/**
 * Redirect
 */
namespace Sledgehammer;
/**
 * The MVC alternative to the redirect() function.
 *
 * Usage: Return a Redirect object as view in your controller.
 *
 * Because redirect() function stops execution of the script, the flow of the MVC classes is interupted.
 * The Redirect class completes the MVC flow and send the headers via the Website->handleRequest()
 *
 * (Compatible with Sledgehammer\HttpServer)
 *
 * @package MVC
 */
class Redirect extends Object implements Document {

	private $url;
	private $permanently;

	function __construct($url, $permanently = false) {
		$this->url = $url;
		$this->permanently = $permanently;
	}

	function getHeaders() {
		return array(
			'http' => array(
				'Status' => ($this->permanently ? '301 Moved Permanently' : '302 Found'),
				'Location' => $this->url
			)
		);
	}

	function isDocument() {
		return true;
	}

	function render() {
		// javascript fallback (headers already sent)
		// Javascript fallback
		echo '<script type="text/javascript">window.location='.json_encode((string) $this->url).';</script>';
		echo '<noscript>';
		// Meta refresh fallback
		echo '<meta http-equiv="refresh" content="0; url='.htmlentities($this->url, ENT_QUOTES).'">';
		// Show a link
		echo '<a href="'.htmlentities($this->url, ENT_QUOTES).'">Continue</a>';
		echo '</noscript>';
	}

}

?>
