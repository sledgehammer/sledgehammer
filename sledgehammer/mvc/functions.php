<?php
/**
 * Global function of the MVC module.
 *
 * @package MVC
 */
// Functions that are available everywhere (global namespace)
namespace {

	/**
	 * render($view) is an alias to $view->render()
	 * but render($view) generates a notice when the $view issn't a View compatible object instead of an fatal error
	 */
	function render($view) {
		if (Sledgehammer\is_valid_view($view)) {
			$view->render();
		}
	}

	/**
	 * Check if the $view parameter is compatible with the View interface via ducktyping.
	 *
	 * @param View $view
	 * @return bool
	 */
	function is_view(&$view = '__UNDEFINED__') {
		return (is_object($view) && method_exists($view, 'render'));
	}

	/**
	 * Genereer een <script src=""> tag, mits deze al een keer gegenereerd is.
	 * @param string $src
	 * @param string $identifier
	 * @return void
	 */
	function javascript_once($src, $identifier = null) {
		static $included = array();

		if ($identifier === null) {
			$identifier = $src;
		}
		if (isset($included[$identifier])) {
			return;
		}
		$included[$identifier] = true;
		echo '<script type="text/javascript" src="'.$src.'"></script>'."\n";
	}

}
// Global functions inside the Sledgehammer namespace
namespace Sledgehammer {

	/**
	 * Geeft de uitvoer van een component als string.
	 * (Uitvoer zoals emails en header() worden niet afgevangen)
	 *
	 * @param View $view
	 * @return string
	 */
	function view_to_string($view) {
		if (is_valid_view($view) === false) {
			return false;
		}
		ob_start();
		try {
			$view->render();
		} catch (\Exception $e) {
			$output = ob_get_clean();
			report_exception($e);
			return $output;
		}
		return ob_get_clean();
	}

	/**
	 * Check if $component is compatible with the View interface, otherwise report notices
	 *
	 * @param View $view
	 * @return bool
	 */
	function is_valid_view(&$view = '__UNDEFINED__') {
		if (is_view($view)) {
			return true;
		}
		if (is_object($view)) {
			notice('Invalid $view, class "'.get_class($view).'" must implement a render() method');
		} elseif ($view == '__UNDEFINED__') {
			notice('Variable is undefined');
		} else {
			notice('Invalid datatype: "'.gettype($view).'", expecting a View object');
		}
		return false;
	}

	/**
	 * Zet een array om naar xml/html parameters; array('x' => 'y') wordt ' x="y"'
	 *
	 * @param array $parameters
	 * @param string $charset  De charset van de parameters (voor htmlentities). Standaard wordt de charset van het actieve document gebruikt.
	 * @return string
	 */
	function implode_xml_parameters($parameterArray, $charset = null) {
		$xml = '';
		if ($charset === null) {
			$charset = Framework::$charset;
		}
		foreach ($parameterArray as $key => $value) {
			$xml .= ' '.$key.'="'.htmlentities($value, ENT_COMPAT, $charset).'"';
		}
		return $xml;
	}

	/**
	 * Zet een string met parameters om naar een array.
	 * ' x="y"' wordt  array('x' => 'y')
	 *
	 * @param string $tag
	 * @return array
	 */
	function explode_xml_parameters($parameterString) {
		/* De reguliere expressies manier kan niet omgaan met values die geen quotes hebben e.d..
		  if (preg_match_all('/(?P<attr>[a-z]*)=[\"\'](?P<value>[a-zA-Z0-9\/._-]*)[\"\']/', $parameterString, $match)) {
		  foreach ($match['attr'] as $index => $key) {
		  $parameters[$key] = $match['value'][$index];
		  }
		  }
		  // */
		$parameters = array();
		$state = 'NAME';
		// Parse the string via a state-machine
		while ($parameterString) {
			switch ($state) {

				case 'NAME': // Zoek de attribuut naam.(de tekst voor de '=')
					$equalsPos = strpos($parameterString, '=');
					if (!$equalsPos) { // er zijn geen attributen meer.
						break 2; // stop met tokenizing
					}
					$value = trim(substr($parameterString, 0, $equalsPos));
					$value = preg_replace('/.*[ \t]/', '', $value); // als er een spatie of tab in de naam staat, haal deze (en alles ervoor) weg
					$attributeName = $value; // attribuutnaam is bekend.

					$parameterString = ltrim(substr($parameterString, $equalsPos + 1)); // De parameterstring inkorten.
					$delimiter = substr($parameterString, 0, 1);
					if ($delimiter != '"' && $delimiter != "'") { // Staan er geen quotes om de value?
						$delimiter = ' \t>';
						$escape = '';
					} else {
						$parameterString = substr($parameterString, 1); // de quote erafhalen.
						$escape = '\\'.$delimiter;
					}
					$state = 'VALUE';
					break;

				case 'VALUE':
					if (preg_match('/^([^'.$delimiter.']*)['.$delimiter.']/', $parameterString, $match)) {
						$parameters[$attributeName] = $match[1]; // De waarde is bekend.
						$parameterString = substr($parameterString, strlen($match[0]));
						$state = 'NAME';
						break;
					} else { // geen delimiter? dan is het de laatste value,
						$parameters[$attributeName] = $parameterString; // De waarde is bekend.
						break 2;
					}

				default:
					error('Invalid state');
			}
		}
		return $parameters;
	}

	/**
	 * Het resultaat van 2 View->getHeaders() samenvoegen.
	 * De waardes in $header1 worden aangevuld en overschreven door de waardes in header2
	 *
	 * @param array $headers
	 * @param array|View $view Een component of een header array
	 * @return array
	 */
	function merge_headers($headers, $view) {
		if (is_string(array_value($headers, 'css'))) {
			$headers['css'] = array($headers['css']);
		}

		if (is_array($view)) { // Is er een header array meegegeven i.p.v. een View?
			$appendHeaders = $view;
		} elseif (method_exists($view, 'getHeaders')) {
			$appendHeaders = $view->getHeaders();
		} else {
			return $headers; // Er zijn geen headers om te mergen.
		}
		foreach ($appendHeaders as $category => $values) {
			switch ($category) {

				case 'title':
					$headers['title'] = $values;
					break;

				case 'css':
				case 'javascript':
					if (is_string($values)) {
						$values = array($values);
					}
					if (empty($headers[$category])) {
						$headers[$category] = $values;
					} else {
						$headers[$category] = array_merge($headers[$category], $values);
					}
					break;

				default:
					if (!is_array($values)) {
						notice('Invalid "'.$category.'" header: values not an array, but a '.gettype($values), array('values' => $values));
					} elseif (empty($headers[$category])) {
						$headers[$category] = $values;
					} else {
						$headers[$category] = array_merge($headers[$category], $values);
					}
					break;
			}
		}
		return $headers;
	}

	/**
	 * Stel de $parameters['class'] in of voegt de $class toe aan de $parameters['class']
	 *
	 * @param string $class
	 * @param array $parameters
	 * @return void
	 */
	function append_class_to_parameters($class, &$parameters) {
		if (isset($parameters['class'])) {
			$parameters['class'] .= ' '.$class;
		} else {
			$parameters['class'] = $class;
		}
	}

	/**
	 * ID and NAME must begin with a letter ([A-Za-z]) and may be followed by any number of letters, digits ([0-9]), hyphens ("-"), underscores ("_"), colons (":"), and periods (".").
	 */
	function tidy_id($cdata) {
		$cdata = trim($cdata);
		$cdata = str_replace(array('[', ']'), '.', $cdata);
		return $cdata;
	}

}
?>