<?php
/**
 * TrueTypeFont
 */
namespace Sledgehammer;
/**
 * ycTIN - TTF class
 * Get the information tables from TrueType font file
 *
 * @name ycTIN - TTF Info class
 * @version 0.1
 * @license GPL 3.0
 * @author Timmy Tin(ycTIN)
 * @website http://www.yctin.com
 * @link http://blog.yctin.com/archives/how-to-get-name-table-from-ttf-font-file-using-php/
 * @package Graphics
 *
 * @package Graphics
 * @history
 * v0.1		get all `name` tables
 */
class TrueTypeFont extends Object {

	public $debug = true;
	private $error_message_tpl = "[ycTIN_TTF][ERROR] {message} <br />n";

	private $filename;
	private $file;
	private $position;
	private $offset;
	private $tables;

	function __construct($filename = false) {
		if (false !== $filename) {
			$this->open($filename);
		}

	}

	function __destruct() {
		$this->close();
	}

	function open($filename) {
		$this->close();
		if (empty($filename)) {
			$this->printError("The filename cannot be empty");
			return false;
		}
		if (! file_exists($filename)) {
			$this->printError("The file $filename does not exist");
			return false;
		}

		$this->filename = $filename;
		$this->file = file_get_contents($filename);
		$this->tables = array();

		if (empty($this->file)) {
			$this->printError("The file $filename is empty");
			return false;
		}
		return true;
	}

	function close() {
		$this->position = $this->offset = 0;
		$this->filename = null;
		$this->file = null;
		$this->tables = null;
	}

	function getNameTable() {
		if (! isset($this->file) || empty($this->file)) {
			$this->printError("Please open the file before getNameTable()");
			return false;
		}

		$num_of_tables = $this->getUint16(4);
		for($i = 0; $i < $num_of_tables; $i ++) {
			if ("name" == $this->getTag(12 + $i * 16)) {
				$this->offset = $this->getUint32(12 + $i * 16 + 8);
				$this->position = $this->offset + 2;
				$num_of_name_tables = $this->getUint16();
				$name_tables_offset = $this->getUint16() + $this->offset;
			}
		}

		$name_tables = array();
		for($i = 0; $i < $num_of_name_tables; $i ++) {
			$this->position = $this->offset + 6 + $i * 12;
			$platform_id = $this->getUint16();
			$specific_id = $this->getUint16();
			$lang_id = $this->getUint16();
			$name_id = $this->getUint16();
			$string_length = $this->getUint16();
			$string_offset = $this->getUint16() + $name_tables_offset;

			$key = "$platform_id::$specific_id::$lang_id";

			if (isset($name_id) && empty($name_tables[$key][$name_id])) {
				$text = substr($this->file, $string_offset, $string_length);
				$name_tables[$key][$name_id] = str_replace(chr(0), "", $text);
			}
		}

		return $this->tables['name'] = $name_tables;
	}

	private function getTag($pt = false) {
		if (false === $pt) {
			$pt = $this->position;
			$this->position += 4;
		}
		return substr($this->file, $pt, 4);
	}

	private function getUint32($pt = false) {
		if (false === $pt) {
			$pt = $this->position;
			$this->position += 4;
		}
		$r = unpack("N", substr($this->file, $pt, 4));
		return $r[1];
	}

	private function getUint16($pt = false) {
		if (false === $pt) {
			$pt = $this->position;
			$this->position += 2;
		}
		$r = unpack("n", substr($this->file, $pt, 2));
		return $r[1];
	}

	private function printError($message) {
		if (true === $this->debug) {
			echo str_replace("{message}", $message, $this->error_message_tpl);
		}
	}
}
?>
