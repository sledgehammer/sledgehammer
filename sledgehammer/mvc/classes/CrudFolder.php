<?php
/**
 * CrudFolder
 */
namespace Sledgehammer;
/**
 * VirtualFolder for basic CRUD operations on a Repository model
 * @todo Support for XML format
 *
 * @package MVC
 */
class CrudFolder extends VirtualFolder {

	public
		$requireDataOnSave = 1; // int Controleer bij de create() & update() of er $_POST data is verstuurd.

	protected
		$model,
		$repository = 'default',
		$primaryKey = 'id', // @var string $id  Wordt gebruik om te id uit de $_REQUEST te halen.  $idValue = $_POST[$this->id]
		$maxRecursion = 0;
	/**
	 *
	 * @param Record $record in static mode
	 * @param array options  array('repository' => 'twitter', 'primaryKey' => 'customer_id')
	 */
	function __construct($model, $options = array()) {
		parent::__construct();
		$this->handle_filenames_without_extension = true;
		$this->model = $model;
		foreach ($options as $option => $value) {
			$this->$option = $value;
		}
	}
	public function generateContent() {
		try {
			return parent::generateContent();
		}
		catch (\Exception $e) {
			return jsonError($e);
		}
	}

	function index($format) {
		$repo = getRepository($this->repository);
		$all = $repo->all($this->model);
		$data = $this->extract($all, $this->maxRecursion + 1);
		return $this->format($data, $format);
	}

	function dynamicFilename($filename) {
		$format = file_extension($filename, $id);
		if ($id === 'list') {
			return $this->index($format);
		}

		$repo = getRepository($this->repository);
		$instance = $repo->get($this->model, $id);

		$data = $this->extract($instance, $this->maxRecursion);
		return $this->format($data, $format);
	}

	/**
	 * Stuur de gegevens van het record naar de client
	 *
	 * @throws Exception on failure
	 * @return Json
	 */
	function read() {
		$repo = getRepository($this->repository);
		$instance = $repo->get($this->model, $_REQUEST[$this->primaryKey]);
		$data = $this->extract($instance, $this->maxRecursion);
		return new Json(array(
			'success' => true,
			$this->model => $data
		));
	}

	/**
	 * @throws Exception on failure
	 * @return Json
	 */
	function update() {
		$repo = getRepository($this->repository);
		$instance = $repo->get($this->model, $_REQUEST[$this->primaryKey]);
		set_object_vars($instance, $this->getNewValues());
		$repo->save($this->model, $instance);
		return new Json(array(
			'success' => true,
		));
	}

	/**
	 * @throws Exception on failure
	 * @return Json
	 */
	function create() {
		$repo = getRepository($this->repository);
		$instance = $repo->create($this->model, $this->getNewValues());
		$repo->save($model, $instance);
		return new Json(array(
			'success' => true,
			$this->primaryKey => $instance->{$this->primaryKey}
		));
	}

	/**
	 * Aan de hand van de id bepalen of er een record toegevoegd of bijgewerkt moet worden.
	 *
	 * @throws Exception on failure
	 * @return Json
	 */
	function save() {
		if (empty($_REQUEST[$this->primaryKey])) {
			// Converteer een eventuele "" naar null
			if (value($_POST[$this->primaryKey]) === '') {
				$_POST[$this->primaryKey] = null;
			}
			return $this->create();
		} else {
			return $this->update();
		}
	}

	/**
	 * De record verwijderen
	 *
	 * @throws Exception on failure
	 * @return Json
	 */
	function delete() {
		$repo = getRepository($this->repository);
		$repo->delete($this->model, $_POST[$this->primaryKey]);
		//throw new Exception('Verwijderen van '.$this->subject.' #'.$_POST[$this->primarykey].' is mislukt');
		return new Json(array(
			'success' => true
		));
	}

	/**
	 * @todo $_POST data filteren zodat eventuele $_POST elementen geen fouten geven set_object_vars()
	 * @return array
	 */
	protected function getNewValues() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			throw new \Exception('Invalid request-method "'.$_SERVER['REQUEST_METHOD'].'", expecting "POST"');
		}
		if(count($_POST) < $this->requireDataOnSave) {
			throw new \Exception('Er zijn onvoldoende gegevens verstuurd. (Minimaal '.$this->requireDataOnSave.' $_POST variabele is vereist)');
		}
		return $_POST;
	}

	/**
	 * Extract the raw properties from a instance
	 * @param mixed $instance
	 * @return array
	 */
	protected function extract($instance, $maxDepth) {
		$data = array();
		foreach ($instance as $property => $value) {
			if (is_object($value) || is_array($value)) {
				if ($maxDepth != 0) {
					$data[$property] = $this->extract($value, $maxDepth - 1);
				}
			} else {
				$data[$property] = $value;
			}
		}
		return $data;
	}

	protected function format($data, $format) {
		if ($format === 'xml') {
			return new XML(XML::build(array($this->model => $data)));
		} else {
			return new Json($data);
		}
	}
}
?>
