<?php
/**
 * Document
 */
namespace Sledgehammer;
/**
 * A Document is a standalone view, that can't be wrapped inside another view.
 * Example documents are: Json, FileDocument, Image and HTMLDocument
 *
 * @package MVC
 */
interface Document extends View {

	/**
	 * Determines if the component is a Document.
	 * This allows errors to be wrapped in a layout.
	 *
	 * @return bool
	 */
	function isDocument();

	/**
	 * The headers for this type of document.
	 * Must include 'http' headers
	 *
	 * @return array
	 */
	function getHeaders();
}
?>
