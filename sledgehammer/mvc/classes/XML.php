<?php
/**
 * XML
 */
namespace Sledgehammer;
/**
 * Render XML output
 *
 * @package MVC
 */
class XML extends Object implements Document {

	/**
	 * @var SimpleXMLElement|DOMDocument|array|string
	 */
	private $xml;

	/**
	 * @var bool  Format XML with indentation and validates the XML.
	 */
	private $formatOutput;

	function __construct($xml, $formatOutput = true) {
		$this->xml = $xml;
		$this->formatOutput = $formatOutput;
	}

	function render() {
		if ($this->xml instanceof \SimpleXMLElement) {
			$xml = $this->xml->asXML();
		} elseif ($this->xml instanceof \DOMDocument) {
			$xml = $this->xml->saveXML();
		} else {
			$xml = $this->xml;
		}
		if ($this->formatOutput) {
			$doc = new \DOMDocument();
			if ($doc->loadXML($xml)) {
				$doc->formatOutput = true;
				$xml = $doc->saveXML();
			}
		}
		echo $xml;
	}

	function isDocument() {
		return true;
	}

	function getHeaders() {
		return array(
			'http' => array(
				'Content-Type' => 'text/xml',
			)
		);
	}

	/**
	 * Convert an array or object to a SimpleXMLElement
	 *
	 * @param array|object $data
	 * @return \SimpleXMLElement
	 */
	static function build($data, $charset = null) {
		if (is_object($data)) {
			$root = get_class($data);
			$elements = get_object_vars($data);
		} else {
			if (count($data) != 1) {
				throw new \Exception('The array should contain only 1 (root)element');
			}
			reset($data);
			$root = key($data);
			$elements = current($data);
		}
		if ($charset === null) {
			$charset = Framework::$charset;
		}
		$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="'.$charset.'"?><'.$root.' />');
		self::addNodes($xml, $elements, $root);
		return $xml;
	}

	/**
	 * @param \SimpleXMLElement $xml
	 * @param array $data
	 */
	private static function addNodes($xml, $data, $node, $detectEncoding = false) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if (is_int($key)) {
					$key = $node;
				}
				$tree = $xml->addChild($key);
				self::addNodes($tree, $value, $key);
			} else {
				$xml->$key = $value;
			}
		}
	}

}

?>
