<?php
/**
 * Template
 */
namespace Sledgehammer;
/**
 * Een component voor het weergeven van php-templates.
 * De templates zijn standaard php. er wordt geen gebruik gemaakt van een tempate engine zoals bv Smarty.
 *
 * @package MVC
 */
class Template extends Object implements View {

	/**
	 * Bestandsnaam van de template (exclusief thema map)
	 * @var string
	 */
	public $template;
	/**
	 * Variabelen die in de template worden gezet. Als je array('naam' => value) meegeeft kun in de template {$naam} gebruiken
	 * @var array
	 */
	public $variables;
	/**
	 * De variable die gebruikt wordt voor de getHeaders()
	 * @var array
	 */
	public $headers;

	/**
	 *
	 * @param string $template
	 * @param array $variables
	 * @param array $headers
	 */
	function __construct($template, $variables = array(), $headers = array()) {
		$this->template = $template;
		$this->variables = $variables;
		$this->headers = $headers;
	}

	/**
	 * Vraag de ingestelde headers op van deze template en eventuele subcomponenten
	 * @return array
	 */
	function getHeaders() {
		$headers = $this->headers;
		$components = $this->getSubviews($this->variables);
		foreach ($components as $component) {
			$headers = merge_headers($headers, $component);
		}
		return $headers;
	}

	/**
	 * De template parsen en weergeven
	 *
	 * @return void
	 */
	function render() {
		static $templateFolders = null;
		if ($templateFolders === null) {
			$templateFolders = array();
			$modules = Framework::getModules();
			foreach ($modules as $module) {
				$templateFolder = $module['path'].'templates/';
				if (file_exists($templateFolder)) {
					$templateFolders[] = $templateFolder;
				}
			}
			$templateFolders = array_reverse($templateFolders);
		}
		// Search templates/ folders.
		foreach ($templateFolders as $folder) {
			if (file_exists($folder.'/'.$this->template)) {
				extract($this->variables);
				return include($folder.'/'.$this->template);
			}
		}
		// Absolute path?
		if (file_exists($this->template)) {
			extract($this->variables);
			return include($this->template);
		}
		warning('Template: "'.$this->template.'" not found', array('folders' => $templateFolders));
	}

	private function getSubviews($array) {
		$views = array();
		foreach ($array as $element) {
			if (is_view($element)) {
				$views[] = $element;
			} elseif (is_array($element)) {
				$views = array_merge($views, $this->getSubviews($element));
			}
		}
		return $views;

	}
}
?>
