<?php
/**
 * HttpError
 */
namespace Sledgehammer;
/**
 * HTTP error page
 * Sends the correct HTTP header and displays an page with the error.
 *
 * @todo Add support for all known HTTP errors http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
 * @package MVC
 */
class HttpError extends Object implements View {

	/**
	 * The HTTP ErrorCode (404, 500, etc)
	 * @var int
	 */
	private $errorCode;
	/**
	 * @var array
	 */
	private $options;

	/**
	 * @param int $statusCode  HTTP Foutcode van de fout 404,403 enz
	 * @param array $options  [optional] Array with additional settings
	 *   notice: Report a notice after render()
	 *   warning: Report a warning after render()
	 *   exception: Report an exception after render()
	 */
	function __construct($errorCode, $options = array()) {
		$this->errorCode = $errorCode;
		$this->options = $options;
	}

	function getHeaders() {
		$error = $this->getError();
		return array(
			'title' => $this->errorCode.' - '.$error['title'],
			'http' => array('Status' => $this->errorCode.' '.$error['header']),
		);
	}

	/**
	 * Render an error page.
	 *
	 * @return void
	 */
	function render() {
		$error = $this->getError();
		$messageBox = new Template('HttpError.php', $error);
		$messageBox->render();
		foreach ($this->options as $option => $value) {
			switch ((string) $option) {

				case 'notice':
				case 'warning':
					$function = $option;
					if (is_array($value)) {
						call_user_func_array($function, $value);
					} else {
						call_user_func($function, $value);
					}
					break;

				case 'exception':
					report_exception($value);
					break;

				default:
					notice('Unknown option: "'.$option.'"', array('value' => $value));
					break;
			}
		}
	}

	private function getError() {

		switch ($this->errorCode) {

			case 400:
				return array(
					'header' => 'Bad Request',
					'icon' => 'error',
					'title' => 'Bad Request',
					'message' => 'Server begreep de aanvraag niet'
				);

			case 401:
				return array(
					'header' => 'Unauthorized',
					'icon'=> 'warning',
					'title' => 'Niet geauthoriseerd',
					'message' => 'U heeft onvoldoende rechten om deze pagina te bekijken.',
				);

			case 403:
				return array(
					'header' => 'Forbidden',
					'icon'=> 'warning',
					'title' => 'Verboden toegang',
					'message' => (substr(URL::getCurrentURL()->path, -1) == '/') ? 'U mag de inhoud van deze map niet bekijken' : 'U mag deze pagina niet bekijken',
				);

			case 404:
				return array(
					'header' => 'Not Found',
					'icon'=> 'warning',
					'title' => 'Bestand niet gevonden',
					'message' => 'De opgegeven URL "'.URL::getCurrentURL().'" kon niet worden gevonden.',
				);

			case 500:
				return array(
					'header' => 'Internal Server Error',
					'icon'=> 'error',
					'title' => 'Interne serverfout',
					'message' => 'Er is een interne fout opgetreden, excuses voor het ongemak.',
				);

			case 501:
				return array(
					'header' => 'Not Implemented',
					'icon' => 'error',
					'title' => 'Not Implemented',
					'message' => 'Dit wordt niet door de server ondersteund'
				);

			default:
				throw new \Exception('HTTP errorCode '.$this->errorCode.' is not (yet) supported.');

		}
	}
}
?>
