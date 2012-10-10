<?php
/**
 * GraphObject
 */
namespace Sledgehammer;
/**
 * An object in the Facebook Graph API.
 *
 * An object oriented interface to the Graph API.
 * Fields and connection are (lazily loaded) properties.
 *
 * @link https://developers.facebook.com/docs/reference/api/
 * @package Facebook
 */
class GraphObject extends Object {

	/**
	 * new: Object created with id: null
	 * constuct: Allow new properties to be added.
	 * id_only: id and parameters are but no api call to fetch fields is made.
	 * partial: Some fields are set, but an  api call based on the id might reveal more fields.
	 * ready: all (allowed) fields are retrieved.
	 *
	 * @var string
	 */
	protected $_state = 'invalid';
	/**
	 * Facebook API call parameters, only available in "id_only" state.
	 * @var array
	 */
	private $_apiParameters;

	/**
	 * The ID of the graph object.
	 * @var number
	 */
	public $id;

	/**
	 * Constructor
	 * @param string $id
	 * @param array $parameters Facebook API call parameters (fields, etc)
	 * @param bool $preload  true: Fetch fields from facebook now. false: Fetch fields from facebook on access.
	 */
	function __construct($id, $parameters = array(), $preload = false) {
		// Unset all properties
		$properties = array_keys(get_public_vars($this));
		unset($properties[array_search('id', $properties)]); // keep the id property
		foreach ($properties as $property) {
			unset($this->$property);
		}
		if ($id === null) {
			$this->_state = 'new';
			return;
		}
		if (is_array($id)) {
			$this->__set($id);
			$this->_state = 'ready';
			unset($this->_apiParameters);
		} elseif ($preload) {
			$this->__set(Facebook::get($id, $parameters));
			$this->_state = 'ready';
			unset($this->_apiParameters);
		} else {
			$this->id = $id;
			$this->_apiParameters = $parameters;
			$this->_state = 'id_only';
		}
	}

	/**
	 * Delete the object from facebook.
	 *
	 * @return bool
	 */
	function delete() {
		if (empty($this->id)) {
			throw new \Exception('Can\'t delete an object without an id');
		}
		return Facebook::delete($this->id);
	}

	/**
	 * Fetch connected grapobjects and store in the property.
	 *
	 * @param string $property
	 * @return mixed
	 */
	function __get($property) {
		if (empty($this->id)) {
			return parent::__get($property);
		}
		$connections = $this->getKnownConnections();
		if (array_key_exists($property, $connections) === false) { // not a (known) connection?
			if ($this->_state === 'id_only') {
				$fields = Facebook::get($this->id, $this->_apiParameters);
				$this->__set($fields);
				$this->_state = 'ready';
				unset($this->_apiParameters);
				if (array_key_exists($property, $fields)) {
					return $fields[$property];
				}
			}
			if ($this->_state === 'partial') {
				$fields = Facebook::get($this->id);
				$this->__set($fields);
				$this->_state = 'ready';
				if (array_key_exists($property, $fields)) {
					return $fields[$property];
				}
			}
			$fields = get_public_vars(get_class($this));
			if (array_key_exists($property, $fields)) { // is the field defined in the class?
				$permissions = static::getFieldPermissions(array('id' => $this->id));
				if (isset($permissions[$property]) && $permissions[$property] !== 'denied' && in_array($permissions[$property], Facebook::getInstance()->getPermissions()) === false) {
					notice('Field "'.$property.'" requires the "'.$permissions[$property].'" permission', 'Current permissions: '.quoted_human_implode(' and ', Facebook::getInstance()->getPermissions()));
				}
				return parent::__get($property);
			}
		}
		try {
			// Retrieve a connection
			if (isset($connections[$property]['class'])) {
				$parameters = array('fields' => call_user_func(array($connections[$property]['class'], 'getAllowedFields')));
			} else {
				$parameters = array();
			}
			$method = 'get'.ucfirst($property);
			$this->__set(array($property => $this->$method($parameters)));
			return $this->$property;
		} catch (\Exception $e) {
			report_exception($e);
			return parent::__get($property);
		}
	}

	/**
	 * Allow adding properties in the "construct" fase.
	 *
	 * @param string|array $property
	 * @param mixed $value
	 */
	function __set($property, $value = null) {
		if (is_array($property)) {
			$state = $this->_state;
			$this->_state = 'construct';
			foreach ($property as $name => $value) {
				$this->$name = $value;
			}
			$this->_state = $state;
			return;
		}
		if ($this->_state === 'construct') {
			$this->$property = $value;
		} else {
			parent::__set($property, $value);
		}
	}

	/**
	 * Handle postTo* methods.
	 *
	 * @param string $method
	 * @param array $arguments
	 */
	function __call($method, $arguments) {
		if (text($method)->startsWith('get')) { // a get*($parameters) method?
			if (empty($this->id)) {
				throw new \Exception('Can\'t fetch a connection without an id');
			}
			if (count($arguments) > 0) {
				$parameters = $arguments[0];
			} else {
				$parameters = array();
			}
			$connection = lcfirst(substr($method, 3));
			$connections = $this->getKnownConnections(array('id' => $this->id));
			if (isset($connections[$connection]['class'])) {
				$class = $connections[$connection]['class'];
			} else {
				$class = '\Sledgehammer\GraphObject';
			}
			if (isset($connections[$connection]['permission']) && $connections[$connection]['permission'] !== 'denied' && in_array($connections[$connection]['permission'], Facebook::getInstance()->getPermissions()) === false) {
				notice('Connection "'.$connection.'" requires the "'.$connections[$connection]['permission'].'" permission', 'Current permissions: '.quoted_human_implode(' and ', Facebook::getInstance()->getPermissions()));
			}
			$objects = array();
			$response = Facebook::all($this->id.'/'.$connection, $parameters);
			foreach ($response as $data) {
				$objects[] = new $class($data);
			}
			if (empty($arguments['fields'])) {
				foreach ($objects as $object) {
					$object->_state = 'partial';
				}
			}
			return new Collection($objects);
		}
		if (text($method)->startsWith('postTo')) { // a postTo*($data) method?
			if (empty($this->id)) {
				throw new \Exception('Can\'t post to a connection without an id');
			}
			if (count($arguments) > 0) {
				$parameters = $arguments[0];
			} else {
				notice('Missing argument 1 for '.$method.'()');
				$parameters = array();
			}
			$response = Facebook::post($this->id.'/'.lcfirst(substr($method, 6)), $parameters);
			return new GraphObject($response['id']);
		} else {
			return parent::__call($method, $arguments);
		}
	}

	/**
	 * Generate fieldlist based on propeties in the currect class.
	 *
	 * @param array $options Options that will be forwarded to the getFieldPermissions() and getKnownConnections() functions.
	 * @return array
	 */
	protected static function getAllowedFields($options = array()) {
		$permissions = static::getFieldPermissions($options);
		$relations = static::getKnownConnections($options);
		$fields = array();
		$availablePermissions = Facebook::getInstance()->getPermissions();
		$properties = array_keys(get_public_vars(get_called_class()));
		foreach ($properties as $property) {
			if (isset($permissions[$property])) { // Does this property require a permission?
				if (in_array($permissions[$property], $availablePermissions)) { // permission granted?
					$fields[] = $property;
				}
			} elseif (array_key_exists($property, $relations) === false) { // Not a relation?
				$fields[] = $property;
			}
		}
		return $fields;
	}

	/**
	 * Fields/properties that depend on permissions. array( field => permission)
	 * @return array
	 */
	protected static function getFieldPermissions() {
		return array();
	}

	/**
	 * Known related objects.
	 * @return array
	 */
	protected static function getKnownConnections() {
		return array();

	}
}

?>
