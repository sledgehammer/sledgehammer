<?php
/**
 * Alert
 */
namespace Sledgehammer;
/**
 * A single alert message.
 *
 * @package MVC
 */
class Alert extends Object implements View {

	/**
	 * The alert body.
	 * @var string html
	 */
	private $message;

	/**
	 * Show a "X "to dismiss the alert.
	 * @var bool
	 */
	protected $close = false;

	/**
	 * Attributes for the html element.
	 * @var array
	 */
	protected $attributes = array(
		'class' => 'alert'
	);

	/**
	 * Constructor
	 * @param string $message HTML
	 * @param array $options
	 */
	function __construct($message, $options = array()) {
		$this->message = $message;
		// Set attributes and properties
		foreach ($options as $option => $value) {
			if (property_exists($this, $option)) {
				$this->$option = $value;
			} else {
				$this->attributes[$option] = $value;
			}
		}
	}

	/**
	 * Render the html.
	 */
	function render() {
		echo HTML::element('div', $this->attributes, true);
		if ($this->close) {
			echo '<button class="close" data-dismiss="alert">&times</button>';
		}
		echo $this->message;
		echo '</div>';
	}

	/**
	 * Create an info alert.
	 * @param string $message HTML
	 * @return Alert
	 */
	static function info($message) {
		return new Alert($message, array('class' => 'alert alert-info'));
	}

	/**
	 * Create an error alert.
	 * @param string $message HTML
	 * @return Alert
	 */
	static function error($message) {
		return new Alert($message, array('class' => 'alert alert-error'));
	}

	/**
	 * Create a success alert.
	 * @param string $message HTML
	 * @return Alert
	 */
	static function success($message) {
		return new Alert($message, array('class' => 'alert alert-success'));
	}

}

?>
