<?php
/**
 * HTMLDocument
 */
namespace Sledgehammer;
/**
 * The container for generating html pages.
 *
 * @package MVC
 */
class HTMLDocument extends Object implements Document {

	/**
	 * Bepaald de template die door de HTMLDocument wordt gebruikt. xhtml, html of ajax
	 * @var string
	 */
	public $doctype;

	/**
	 * The value of the "ContentType: " header.
	 * Set to "application/xhtml+xml" for XHTML
	 */
	public $contentType = 'text/html';

	/**
	 * @var View
	 */
	public $content;
	/**
	 * Bepaald of de statusbalk getoond word. (Wordt automatisch bepaald door de ErrorHandler->html waarde)
	 * @var bool
	 */
	public $showStatusbar;

	/**
	 *  Tags die in de <head> vallen
	 *
	 * title, // De <title> tag
	 * css = array(), // De stylesheet urls
	 * javascript = array(), // De javascript urls (Bij voorkeur geen javascript in de head.)
	 *
	 * meta = array(), // De <meta> tags
	 * link = array(), // De <link> tags
	 * htmlParameters = array(); // parameters die binnen de <html> tag geplaatst worden
	 * bodyParameters = array(); // parameters die binnen de <body> tag geplaatst worden
	 * @var array
	 */
	private $headers;

	function __construct($doctype = 'html') {
		$this->doctype = $doctype;
		$this->contentType = 'text/html; charset='.strtolower(Framework::$charset);
		$this->showStatusbar = Framework::$errorHandler->html; // Als er html error getoond mogen worden, toon dan ook de statusbalk.
	}

	/**
	 * Vraag de headers op en werk de interne headers array bij.
	 * @return array
	 */
	function getHeaders() {

		$headers = array(
			'http' => array(
				'Content-Type' => $this->contentType,
			),
			'charset' => Framework::$charset,
			'htmlParameters' => array(),
			'bodyParameters' => array(),
		);

		if (defined('WEBPATH') && WEBPATH != '/' && file_exists(PATH.'application/public/favicon.ico')) {
			$headers['link']['favicon'] = array('rel' => 'shortcut icon', 'href' => WEBROOT.'favicon.ico', 'type' => 'image/x-icon');
		}
		// $headers['http']['Content-Type'] = 'application/xhtml+xml';
		if (Framework::$errorHandler->html) {
			$headers['css']['debug'] = WEBROOT.'core/css/debug.css';
		}
		$this->headers = merge_headers($headers, $this->content);
		if (empty($this->headers['title'])) {
			notice('getHeaders() should contain a "title" element for a HTMLDocument');
		}
		return $this->headers;
	}

	/**
	 * Het document genereren
	 *
	 * @return void
	 */
	function render() {
		if ($this->headers == null) {
			notice(get_class($this).'->getHeaders() should be executed before '.get_class($this).'->render()');
		}
		$variables = array(
			'charset' => $this->headers['charset'],
			'title' => array_value($this->headers, 'title'),
			'head' => array(),
			'htmlParameters' => implode_xml_parameters($this->headers['htmlParameters']),
			'bodyParameters' => implode_xml_parameters($this->headers['bodyParameters']),
			'body' => $this->content,
			'showStatusbar' => $this->showStatusbar,
		);

		$validHeaders = array('http', 'title', 'charset', 'css', 'meta', 'link', 'javascript', 'htmlParameters', 'bodyParameters');
		foreach ($this->headers as $key => $value) {
			if (!in_array($key, $validHeaders)) {
				notice('Invalid header: "'.$key.'", expecting "'.human_implode('" or "', $validHeaders, '", "').'"');
			}
		}

		// tags binnen de <head> instellen
		$head = array(
			'meta' => array(),
			'link' => array(),
		);
		if (isset($this->headers['meta'])) {
			$head['meta'] = $this->headers['meta'];
		}
		if (isset($this->headers['link'])) {
			$head['link'] = $this->headers['link'];
		}
		if (isset($this->headers['css'])) {
			foreach ($this->headers['css'] as $url) {
				$head['link'][] = array('href' => $url, 'type' => 'text/css', 'rel' => 'stylesheet');
			}
		}
		$eot = ($this->doctype === 'xhtml') ? ' />' : '>'; // End of Tag instellen
		foreach ($head as $tag => $tags) {
			foreach ($tags as $parameters) {
				$variables['head'][] = '<'.$tag.implode_xml_parameters($parameters).$eot;
			}
		}
		if (isset($this->headers['javascript'])) {
			foreach ($this->headers['javascript'] as $identifier => $url) {
				if (is_int($identifier)) {
					$identifier = $parameters['src'];
				}
				ob_start();
				javascript_once($url, $identifier);
				$variables['head'][] = ob_get_clean();
			}
		}
		$template = new Template('doctype/'.$this->doctype.'.php', $variables);
		$template->render();
	}

	function isDocument() {
		return true;
	}
}
?>