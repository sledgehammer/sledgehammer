<?php
/**
 * Example App
 */
namespace Sledgehammer;
class App extends Website {

	/**
	 * Public methods are accessable as file and must return a View object.
	 * "/index.html"
	 * @return View
	 */
	function index() {
		return new Nav(array(
			'Welcome',
			WEBROOT.'example/item1.html' => 'Item 1',
			WEBROOT.'service.json' => 'Item 2',
		), array(
			'class' => 'nav nav-list'
		));
	}

	/**
	 * Public methods with the "_folder" suffix are accesable as folder.
	 * "/example/*"
	 * @param string $file
	 * @return View
	 */
	function example_folder($file) {
		return new Alert('This is page: '.$file);
	}

	function service() {
		return new Json(array('success' => true));
	}

	protected function wrapContent($view) {
		$headers = array(
			'title' => 'Sledgehammer App',
			'css' => WEBROOT.'mvc/css/bootstrap.css',
		);
		return new Template('layout.php', array('content' => $view), $headers);
	}
}

?>