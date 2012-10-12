<?php
/**
 * Pagination
 */
namespace Sledgehammer;
/**
 * << 1 2 3 [4] 5 6 7 8 9 10 ... 42 >>
 *
 * @package MVC
 */
class Pagination extends Object implements View {

	/**
	 * Number of pages.
	 * @var int
	 */
	private $count;

	/**
	 * The current page
	 * @var int
	 */
	private $current;
	// Options

	private $parameter = 'page';
	private $max = 11;

	/**
	 * @var string
	 */
	private $href;

	/**
	 * @var string Alignent "left", "center", "right"
	 */
	private $align = 'left';

	/**
	 *
	 * @param int $count     Number of pages
	 * @param int $current    Current page
	 * @param array $options [optional]
	 */
	function __construct($count, $current = 1, $options = array()) {
		$this->count = $count;
		$this->current = $current;
		foreach ($options as $key => $value) {
			$this->$key = $value;
		}
		if ($this->href === null) {
			$url = URL::getCurrentURL();
			unset($url->query[$this->parameter]);
			$url->query[$this->parameter] = '';
			$this->href = (string) $url;
		}
	}

	function render() {
		if ($this->count == 0) { // No pages, no pagination
			return;
		}
		$start = 1;
		$end = $this->count;
		$pages = array();
		$class = 'pagination';
		if ($this->align != 'left') {
			$class .= ' pagination-'.$this->align;
		}
		echo "<div class=\"".$class."\"><ul>\n";
		// previous
		if ($this->current != 1) {
			echo "\t<li>", HTML::element('a', array('href' => $this->href.($this->current - 1)), '&laquo;'), "</li>\n";
		}
		if ($this->count > $this->max) {
			$offset = floor($this->max / 2);
			if ($this->current < $offset + 2) {
				$end = $this->max - 1;
			} else {
				if ($this->current + $offset > $this->count) {
					$start = $this->count - $this->max;
				} else {
					$start = $this->current - $offset;
				}
				$end = $this->current + $offset;
				if ($end > $this->count) {
					$end = $this->count;
				}
			}
		}
		// numbers
		for ($i = $start; $i <= $end; $i++) {
			$attributes = ($i == $this->current) ? array('class' => 'active') : array();
			echo "\t", HTML::element('li', $attributes, HTML::element('a', array('href' => $this->href.$i), $i)), "\n";
		}
		// total pages indication
		if ($end != $this->count) {
			if ($end != ($this->count - 1)) {
				$nextDecimal = (floor($this->current / 10) * 10) + 10;
				if ($nextDecimal > $this->count) {
					$nextDecimal = $this->count;
				}
				echo "\t<li>", HTML::element('a', array('href' => $this->href.$nextDecimal), '...'), "</li>\n"; // @todo jump to page
			}
			echo "\t<li>", HTML::element('a', array('href' => $this->href.($this->count)), $this->count), "</li>\n";
		}
		// next
		if ($this->current != $this->count) {
			echo "\t<li>", HTML::element('a', array('href' => $this->href.($this->current + 1)), '&raquo;'), "</li>\n";
		}
		echo "</ul></div>";
	}

}

?>
