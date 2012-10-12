<?php
/**
 * GoogleAnalytics
 */
namespace Sledgehammer;
/**
 * Een component voor het weergeven van de google analytics tracker js code
 *
 * @package MVC
 */
class GoogleAnalytics extends Object implements View {

	public
		$code;

	/**
	 *
	 * @param string $code  "UA-xxxxxxx-x"
	 */
	function __construct($code) {
		//@todo code valideren
		$this->code = $code;
	}

	function render() {
		$srcPrefix = (value($_SERVER['HTTPS']) == 'on') ? 'https://ssl' : 'http://www';
		echo '<script type="text/javascript">'."\n";
		echo "	var _gaq = _gaq || [];\n";
		echo "	_gaq.push(['_setAccount','{$this->code}']);\n";
		echo "	_gaq.push(['_trackPageview']);\n";
		echo "	(function() {\n";
		echo "		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
		echo "		ga.src = '{$srcPrefix}.google-analytics.com/ga.js';\n";
		echo "		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
		echo "	})();\n";
		echo '</script>';
	}
}
?>
