<?php
/**
 * Facebook
 */
namespace Sledgehammer;
/**
 * Helper for using the Facebook Open Graph API or executing FQL
 * @link https://developers.facebook.com/docs/reference/api/
 * @link https://developers.facebook.com/docs/reference/fql/
 */
class Facebook extends \BaseFacebook {

	/**
	 * Automaticly redirect to the login page when there is no active access token.
	 * @var bool
	 */
	public $autoLogin = true;

	/**
	 * Logs all the facebook requests.
	 * @var Logger
	 */
	public $logger;

	/**
	 * The default limit for paged results retrieved via Facebook->all().
	 * 10 seems low, but with 1 a 2 sec per api call, it already takes 20+ sec.
	 * @var int
	 */
	public $defaultPagerLimit = 10;

	/**
	 * The permissions the app requires.
	 * @link https://developers.facebook.com/docs/authentication/permissions/
	 * @var array|string
	 */
	private $requiredPermissions = array();

	/**
	 * Facebook singleton
	 * @var Facebook
	 */
	private static $instance;

	/**
	 * Current user (singleton)
	 * @var FacebookUser
	 */
	private static $me;

	/**
	 * Facebook application (singleton)
	 * @var GraphObject
	 */
	private static $application;

	/**
	 * Use Facebook::configure() to initialize the facebook instance.
	 *
	 * @param string $appId
	 * @param string $appSecret
	 * @param array $options
	 */
	function __construct($appId, $appSecret, $permissions = array(), $options = array()) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		if (is_array($permissions)) {
			$permissions = implode(',', $permissions);
		}
		$this->requiredPermissions = $permissions;
		$state = $this->getPersistentData('state');
		if (!empty($state)) {
			$this->state = $state;
		}
		$accessToken = $this->getPersistentData('access_token');
		if (!empty($accessToken)) {
			$this->accessToken = $accessToken;
		}
		if (isset($options['ignore_session_state'])) { // Option to prevent the "No session started" warning.
			$ignoreSessionState = $options['ignore_session_state'];
			unset($options['ignore_session_state']);
		} else {
			$ignoreSessionState = false;
		}
		if ($ignoreSessionState == false && session_id() == false) {
			warning('No session started');
		}
		foreach ($options as $property => $value) {
			$this->$property = $value;
		}
		$this->logger = new Logger(array(
			'identifier' => 'Facebook',
			'singular' => 'request',
			'plural' => 'requests',
			'renderer' => 'Sledgehammer\Facebook::renderLog',
			'columns' => array('Method', 'Request', 'Duration'),
		));
	}

	/**
	 * Redirect to the Facebook loginUrl to retrieve an active accessToken.
	 *
	 * When no 'scope' is given, the $this->requiredPermissions are used.
	 *
	 * @tip To use an redirect_url to Facebook Page and prevent a 191 error, change "?v=" to "?sk=" in the pageurl. https://www.facebook.com/pages/$pagename/$pageId?sk=app_$appId
	 *
	 * @param array $parameters  List with optional parameters
	 *   'display' => 'popup'
	 *   'scope' => array('email','read_stream', etc) @link https://developers.facebook.com/docs/authentication/permissions/
	 *   'redirect_url'> callback url
	 * @return true
	 */
	function login($parameters = array()) {
		$this->autoLogin = false;
		if (isset($_GET['error']) || isset($_GET['error_reason'])) {
			throw new \Exception($_GET['error_description']);
		}
		if (isset($parameters['scope']) === false) {
			$parameters['scope'] = $this->requiredPermissions;
		}
		$permissions = $parameters['scope'];
		if (is_string($permissions)) {
			$permissions = explode(',', $parameters['scope']);
		}
		$accessToken = false;
		if (isset($_GET['code'])) {
			$accessToken = $this->getUserAccessToken(); // Retrieves accesstoken and calls setPersistentData()
		} elseif (isset($_REQUEST['signed_request'])) {
			if ($this->getUser() != 0) {
				$accessToken = $this->getUserAccessToken(); // Retrieves accesstoken and calls setPersistentData()
			}
		}
		if ($accessToken) {
			$this->setAccessToken($accessToken);
			if (count($permissions) > 0) {
				// Validate permissions
				$acceptedPermissions = $this->api('me/permissions');
				foreach ($permissions as $permission) {
					if (isset($acceptedPermissions['data'][0][$permission]) === false || $acceptedPermissions['data'][0][$permission] != 1) {
						$this->clearAllPersistentData();
						throw new InfoException('Permission to "'.$permission.'" was denied', array('Granted permissions' => $acceptedPermissions['data'][0]));
					}
				}
			}
			return true;
		}
		if (session_id() == false) {
			throw new \Exception('Unable to login to facebook, a session is required');
		}
		$parameters['scope'] = implode(',', $permissions);
		$this->clearAllPersistentData();
		if (isset($_REQUEST['signed_request'])) { // Inside a Facebook Canvas/Page
			$parameters['redirect_uri'] = $_SERVER['HTTP_REFERER']; // Return to the page/canvas
			echo '<script type="text/javascript">window.top.location='.json_encode($this->getLoginUrl($parameters)).';</script>';
			exit();
		} else {
			redirect($this->getLoginUrl($parameters));
		}
	}

	/**
	 * Check if an AccessToken is available.
	 * @param bool $validate Perform an API call to check if the AccessToken is still active.
	 * @return bool
	 */
	function isLoggedIn($validate = false) {
		$loggedIn = ($this->getAccessToken() != $this->getApplicationAccessToken());
		if ($loggedIn && $validate) {
			$autoLogin = $this->autoLogin;
			$this->autoLogin = false; // Temporarily disable autoLogin
			try {
				$this->api('/me', 'GET', array('fields' => 'id'));
				$this->autoLogin = $autoLogin;
				return true;
			} catch (\Exception $e) {
				$this->autoLogin = $autoLogin;
				$this->clearPersistentData('access_token');
				return false;
			}
		}
		return $loggedIn;
	}

	/**
	 * Make an API call.
	 *
	 * @throws Exceptions on failure
	 * @return mixed response
	 */
	function api(/* polymorphic */) {
		$arguments = func_get_args();
		// Normalize fields parameter.
		if (isset($arguments[2]['fields']) && is_array($arguments[2]['fields'])) {
			$arguments[2]['fields'] = implode(',', $arguments[2]['fields']);
		}
		// Check cache
		if (isset($arguments[2]['local_cache']) && $arguments[2]['local_cache']) { // Enable caching for the request?
			$cache = sha1(json_encode($arguments));
			if (isset($_SESSION['__Facebook__']['cache'][$cache])) {
				return $_SESSION['__Facebook__']['cache'][$cache]; // Cache hit
			}
			unset($arguments[2]['local_cache']);
		}
		// Execute the Facebook API call.
		try {
			$response = call_user_func_array('parent::api', $arguments);
		} catch (\FacebookApiException $e) {
			// Detect if the error was caused by an invalid accessToken
			if ($this->autoLogin == false || ($_SERVER['REQUEST_METHOD'] != 'GET' && empty($_REQUEST['signed_request']))) {
				throw $e;
			}
			$messages = array(
				'An active access token must be used to query information about the current user\.', // Not logged in
				'Error validating access token: User [0-9]+ has not authorized application [0-9]+\.', // Was logged in, but user uninstalled the application.
				'Error validating access token: Session has expired at unix time [0-9]+\. The current unix time is [0-9]+\.', // Access token timed out
				'Error validating access token: The session was invalidated explicitly using an API call\.'
			);
			$invalidAccessToken = false;
			$errorMessage = $e->getMessage();
			foreach ($messages as $message) {
				if (preg_match('/^'.$message.'$/', $errorMessage)) {
					$invalidAccessToken = true;
					break;
				}
			}
			if ($invalidAccessToken === false) { // Not an authentication error?
				throw $e;
			}
			$this->destroySession(); // Remove the invalid access token.
			if ($this->login()) {
				// Automatic login was successful, retry api call.
				$response = call_user_func_array('parent::api', $arguments);
			} else {
				throw $e;
			}
		}

		if (isset($cache)) {
			$_SESSION['__Facebook__']['cache'][$cache] = $response;
		}
		return $response;
	}

	/**
	 * Makes an HTTP request. This method can be overridden by subclasses if
	 * developers want to do fancier things or use something other than curl to
	 * make the request.
	 *
	 * @param string $url The URL to make the request to
	 * @param array $params The parameters to use for the POST body
	 * @param CurlHandler $ch Initialized curl handle
	 *
	 * @return string The response text
	 */
	protected function makeRequest($url, $params, $ch = null) {
		$options = \BaseFacebook::$CURL_OPTS;
		$options[CURLOPT_URL] = $url;
		if ($this->getFileUploadSupport()) {
			$options[CURLOPT_POSTFIELDS] = $params;
		} else {
			$options[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
		}
		$start = microtime(true);
		$request = new cURL($options);
		$result = $request->getContent();
		$this->logger->append($url, array(
			'params' => $params,
			'duration' => (microtime(true) - $start)
		));
		return $result;
	}

	/**
	 * Get the permissions/scope of the current user.
	 * @return array
	 */
	function getPermissions() {
		$response = $this->api('me/permissions', 'GET', array('local_cache' => true));
		$permissions = array();
		foreach ($response['data'][0] as $permission => $enabled) {
			if ($enabled) {
				$permissions[] = $permission;
			}
		}
		return $permissions;
	}

	/**
	 * Set the Application ID.
	 *
	 * @param string $appId The Application ID
	 * @return BaseFacebook
	 */
	function setAppId($appId) {
		if ($this->appId !== $appId) {
			$this->clearCache();
		}
		$this->appId = $appId;
		return $this;
	}

	/**
	 * Sets the access token for api calls.  Use this if you get
	 * your access token by other means and just want the SDK
	 * to use it.
	 *
	 * @param string $accessToken an access token.
	 * @return BaseFacebook
	 */
	function setAccessToken($accessToken) {
		if ($accessToken !== $this->getPersistentData('access_token')) {
			$this->clearCache();
		}
		$this->accessToken = $accessToken;
		return $this;
	}

	/**
	 * Configure the global Facebook instance.
	 *
	 * @param string $appId
	 * @param string $appSecret
	 * @param string|array $permissions Scope of the application. @link https://developers.facebook.com/docs/authentication/permissions/
	 * @param array $options optional settings: array(
	 *  'fileUploadSupport' => bool,
	 *  'autoLogin' => bool,
	 *  'logLimit' => int,
	 *  'defaultPagerLimit' => int
	 *  'signedRequest' => string
	 *  'accessToken' => string,
	 * )
	 * @return void
	 */
	static function configure($appId, $appSecret, $permissions = array(), $options = array()) {
		self::$instance = new Facebook($appId, $appSecret, $permissions, $options);
	}

	/**
	 * Returns the Facebook instance.
	 *
	 * @return Facebook
	 */
	static function getInstance() {
		if (self::$instance === null) {
			throw new InfoException('Facebook AppID was not configured', 'Use Facebook::configure($appId, $appSecret); to configure your AppID.');
		}
		return self::$instance;
	}

	/**
	 * Current user (singleton).
	 *
	 * @return Facebook\User
	 */
	static function me() {
		if (self::$me === null) {
			self::$me = new Facebook\User(self::getInstance()->getPersistentData('user_id', 'me'), null, true);
		}
		return self::$me;
	}

	/**
	 * Current application (singleton)
	 *
	 * @return Facebook\Application
	 */
	static function application() {
		if (self::$application === null) {
			self::$application = new Facebook\Application(self::getInstance()->getAppId(), null, true);
		}
		return self::$application;
	}

	/**
	 * Short notation for the api GET requests
	 *
	 * @param string $path
	 * @param array $parameters
	 * @return mixed
	 */
	static function get($path, $parameters = array()) {
		return self::getInstance()->api($path, 'GET', $parameters);
	}

	/**
	 * Retrieve data via an FQL (Facebook Query Language) query
	 * @link https://developers.facebook.com/docs/reference/fql/
	 *
	 * @param string $fql FQL Query
	 * @param array $options  array('local_cache' => bool)
	 * @return array
	 */
	static function query($fql, $options = array()) {
		$request = array(
			'method' => 'fql.query',
			'query' => $fql,
			'callback' => ''
		);
		if (array_value($options, 'local_cache')) {
			if (empty($_SESSION['__Facebook__']['cache'][$fql])) { // // Cache miss?
				$_SESSION['__Facebook__']['cache'][$fql] = self::getInstance()->api($request);
			}
			return $_SESSION['__Facebook__']['cache'][$fql];
		}
		return self::getInstance()->api($request);
	}

	/**
	 * Fetch all pages in a paginated result.
	 *
	 * @param string $path
	 * @param array $parameters
	 * @return array
	 */
	static function all($path, $parameters = array(), $pagerLimit = null) {
		$facebook = self::getInstance();
		if (isset($parameters['limit']) || isset($parameters['offset'])) { // The request is for a specific page
			return $facebook->api($path, 'GET', $parameters);
		}
		if ($pagerLimit === null) {
			$pagerLimit = $facebook->defaultPagerLimit = 10;
		}
		$page = 0;
		$pages = array();
		$url = new URL($path);
		$url->query = $parameters;
		while (true) {
			if ($page > $pagerLimit) {
				notice('Maximum pager limit ('.$pagerLimit.') was reached');
				break;
			}
			$response = $facebook->api($url->path, 'GET', $url->query); // fetch page
			if ($page === 1 && $pages[0] === $response['data']) { // Does page 2 have identical results as page 1?
				// Bug/loop detected in facebook's pagin.
				// Example loop: /$friend_id/mutualfriends/$me_id
				return $response['data']; // return a single.
			}
			$pages[$page] = $response['data'];
			if (empty($response['paging']['next']) == false) {
				$url = new URL($response['paging']['next']);
				if (isset($url->query['limit']) && ((count($response['data']) / $url->query['limit']) < 0.10)) { // This page has less than 10% results of the limit?
					// 90+% is filtered out or there is an error/loop in facebooks paging
					// Example empty 2nd page: /me/friends
					// Example loop: /$friend_id/mutualfriends/$me_id
					break; // Assumme facebook loop/empty second page.
				}
			} else {
				// no more pages
				break;
			}
			$page++;
		}
		$data = array();
		foreach ($pages as $page) {
			if (is_array($page)) { // [havelaer]: check if page is array
				$data = array_merge($data, $page);
			}
		}
		return $data;
	}

	/**
	 * Short notation for the api POST requests
	 *
	 * @param string $path
	 * @param array|GraphObject $data
	 * @return mixed
	 */
	static function post($path, $data = array()) {
		if (is_object($data)) {
			$data = get_public_vars($data);
			foreach ($data as $field => $value) {
				if ($value instanceof Collection) {
					unset($data[$field]);
				}
			}
		}
		return self::getInstance()->api($path, 'POST', $data);
	}

	/**
	 * Short notation for the api DELETE requests
	 *
	 * @param string $path
	 * @param array $parameters
	 * @return mixed
	 */
	static function delete($path, $parameters = array()) {
		return self::getInstance()->api($path, 'DELETE', $parameters);
	}

	/**
	 * Helper method rendering the $this->logger entries.
	 *
	 * @param string $entry url|fql
	 * @param array $meta
	 * @return void
	 */
	static function renderLog($entry, $meta) {
		$params = $meta['params'];
		if (empty($params['method'])) {
			echo '<td>OAUTH</td><td>', $entry, '? ...</td>';
		} elseif ($params['method'] === 'fql.query') {
			echo '<td>FQL</td><td>';
			echo HTML::element('a', array('href' => 'https://developers.facebook.com/tools/explorer?fql='.urlencode($params['query'])), $params['query']);
			echo '</td>';
		} else {
			$method = $params['method'];
			echo'<td>', $method, '</td><td>';
			$url = $entry;
			if (count($params) !== 2) {
				unset($params['method']);
				unset($params['access_token']);
				$url .= '?'.http_build_query($params);
			}
			$content = (strlen($url) < 150) ? $url : substr($url, 0, 150).'&nbsp;...';
			if (substr($url, 0, 27) === 'https://graph.facebook.com/') {
				$url = 'https://developers.facebook.com/tools/explorer?method='.$method.'&path='.urlencode(substr($url, 27));
			}
			echo HTML::element('a', array('href' => $url, 'target' => '_blank'), $content);
		}
		$duration = $meta['duration'];
		if ($duration > 2) {
			$color = 'logentry-alert';
		} elseif ($duration > 0.9) {
			$color = 'logentry-warning';
		} else {
			$color = 'logentry-debug';
		}
		echo '<td class="logentry-number ', $color, '"><b>', format_parsetime($duration), '</b>&nbsp;sec</td>';
	}

	protected function clearCache() {
		self::$me = null;
		self::$application = null;
		$this->user = null;
		$this->clearPersistentData('cache');
	}

	protected function clearAllPersistentData() {
		unset($_SESSION['__Facebook__']);
		self::$me = null;
		self::$application = null;
	}

	protected function clearPersistentData($key) {
		unset($_SESSION['__Facebook__'][$key]);
	}

	protected function getPersistentData($key, $default = false) {
		if (isset($_SESSION['__Facebook__'][$key])) {
			return $_SESSION['__Facebook__'][$key];
		}
		return $default;
	}

	protected function setPersistentData($key, $value) {
		$_SESSION['__Facebook__'][$key] = $value;
		return $this;
	}

}

?>
