<?php
/**
 * FileDocument
 */
namespace Sledgehammer;
/**
 * Een bestand op het bestandsysteem naar de client sturen.
 * De MVC variant van de render_file() functie.
 *
 * @package MVC
 */
class FileDocument extends Object implements Document {

	public
		$headers = array();

	private
		$filename,
		$error = false,
		$notModified = false,
		$etag,
		$fileContents;

	/**
	 * @param array $options  array(
	 *	'etag'=> bool,
	 *  'file_get_contents' => bool,
	 * )
	 */
	function __construct($filename, $options = array('etag' => false)) {
		$this->filename = $filename;
		$this->etag = array_value($options, 'etag');
		if (!file_exists($filename)) {
			if (basename($filename) == 'index.html') {
				$this->error = new HttpError(403);
			} else {
				$this->error = new HttpError(404);
			}
			return;
		}
		$last_modified = filemtime($filename);
		if ($last_modified === false) {
			$this->error = new HttpError(500);
			return;
		}
		if (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)) {
			$if_modified_since = strtotime(preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']));
			if ($if_modified_since >= $last_modified) { // Is the Cached version the most recent?
				$this->notModified = true;
				return;
			}
		}
		if ($this->etag) {
			$etag = md5_file($filename);
			if (array_value($_SERVER, 'HTTP_IF_NONE_MATCH') === $etag) {
				$this->notModified = true;
				return;
			}
			$this->headers[] = 'ETag: '.md5_file($filename);
		}
		$this->notModified = false;
		if (is_dir($filename)) {
			$this->error = new HttpError(403);
			return;
		}
		$this->headers['Content-Type'] = mimetype($filename);
		$this->headers['Last-Modified'] = gmdate('r', $last_modified);
		$filesize = filesize($filename);
		if ($filesize === false) {
			$this->error = new HttpError(500);
			return;
		}
		$this->headers['Content-Length'] = $filesize; // @todo Detecteer bestanden groter dan 2GiB, deze geven fouten.
		if (array_value($options, 'file_get_contents')) {
			$this->fileContents = file_get_contents($filename);
		}
	}

	function getHeaders() {
		if ($this->error) { // Is er een fout opgetreden?
			return $this->error->getHeaders();
		}
		if ($this->notModified) { // Is het bestand niet aangepast?
			return array('http' => array(
				'Status' => '304 Not Modified'
			));
		}
		// Het bestand bestaat en kan verstuurd worden.
		return array('http' => $this->headers);
	}

	function render() {
		if ($this->error) { // Is er een fout opgetreden?
			$this->error->render();
			return;
		}
		if ($this->notModified) { // Is het bestand niet aangepast?
			return; // De inhoud van het bestand NIET versturen
		}
		if ($this->fileContents !== null) {
			echo $this->fileContents;
		} else {
			readfile($this->filename);
		}
	}
/*
	function render() {
		if ($this->error) {
			if ($this->error == 404) { // Bij een 404 error een notice geven. De 500's geven al een notice.
				notice('HTTP[404] File "'.URL::uri().'" not found');
			} elseif ($this->error == 403) { // De 403 error wel loggen maar niet mailen.
				error_log('HTTP[403] Directory listing for "'.URL::uri().'" not allowed');
			}
			$httpError = new HttpError($this->error);
			$component = $httpError->execute();
			$component->render();
			return;
		}
		send_headers($this->headers);
		if ($this->notModified) {
			return;
		}
		readfile($this->filename);
	}
 */

	/**
	 * @return bool
	 */
	function isDocument() {
		if ($this->error) {
			return false; // Als het bestand niet betaat, geeft dan een foutmelding in de layout van de website
		}
		return true;
	}
}
?>
