<?php
/**
 * BuildFacebookClassFromDocumentation
 */
namespace Sledgehammer;
/**
 * Generate a Facebook class from the facebook api documentation html.
 */
class BuildFacebookClassFromDocumentation extends Util {

	public function generateContent() {
		// Can't download the documectation directly (required facebook login)
		$form = new Form(array(
					'class' => 'form-horizontal',
					'legend' => 'Paste HTML from https://developers.facebook.com/docs/reference/api/$model/',
					'fields' => array(
						'HTML' => new Input(array('type' => 'textarea', 'name' => 'source')),
						new Input(array('type' => 'submit', 'value' => 'Generate class', 'class' => 'btn btn-primary')),
					)
				));
		$data = $form->import($error);
		if ($data) {
			$dom = new \DOMDocument();
			@$dom->loadHTML($data['source']);
			$xml = simplexml_import_dom($dom);
			$link = $xml->xpath('//div[@class="breadcrumbs"]/a[last()]');
			$path = explode('/', trim($link[0]['href'], '/'));
			$elements = $xml->xpath('//div[@id="bodyText"]');
			$info = array(
				'class' => ucfirst(end($path)),
				'link' => 'https://developers.facebook.com/'.implode('/', $path).'/',
				'description' => strip_tags($elements[0]->p[0]->asXML()),
				'fields' => $this->extractFields($elements[0]->table[0]->tr),
				'connections' => array(),
				'constructor' => strpos($elements[0]->table[0]->asXML(), 'only returned if') // ... "specifically requested via the fields URL parameter"
			);
			if ($elements[0]->table[1] !== null) {
				$info['connections'] = $this->extractFields($elements[0]->table[1]->tr);
			}
			if (count($info['fields']) == 0 && count($info['connections']) != 0) { // Thread documentation
				$info['fields'] = $info['connections'];
				$info['connections'] = $this->extractFields($elements[0]->table[2]->tr);
			}
			return new Dump($this->generatePhp($info));
		} else {
			return $form;
		}
	}

	/**
	 * Extract info from the rows in a "Fields" or  "Connections" table.
	 * @param \SimpleXMLElement $rows
	 * @return array
	 */
	function extractFields(\SimpleXMLElement $rows) {
		if ($rows->count() == 0 || $rows[0]->td->count() != 4) { // Not a "Name, Description, Permissions, Returns" table?
			return array();
		}
		$fields = array();
		foreach ($rows as $row) {
			if ($row->td[0]->b == 'Name') {
				continue; // Skip table header
			}
			$field = array(
				'name' => strip_tags($row->td[0]->asXML()),
				'description' => strip_tags($row->td[1]->children()->asXML()),
				'permissions' => array(),
				'returns' => strip_tags($row->td[3]->children()->asXML()),
			);
			foreach ($row->td[2]->p->children()->code as $permission) {
				if (in_array((string) $permission, array('access_token'))) {
					continue; // skip "access_token"
				}
				$field['permissions'][] = (string) $permission;
			}
			$fields[$field['name']] = $field;
		}
		return $fields;
	}

	/**
	 *
	 * @param array $info
	 */
	function generatePhp($info) {
		$php = "<?php\n";
		$php .= "/**\n";
		$php .= " * ".$info['class']."\n";
		$php .= " */\n";
		$php .= "namespace Sledgehammer\\Facebook;\n";
		$php .= "\n";
		$php .= "use Sledgehammer\Collection;\n";
		$php .= "use Sledgehammer\GraphObject;\n";
		$php .= "\n";
		$php .= "/**\n";
		$php .= " * ".$info['description']."\n";
		if (count($info['fields']['id']['permissions'])) {
			$php .= " * Requires ".quoted_human_implode(' or ', $info['fields']['id']['permissions'])." permissions.\n";
		}
		$php .= " *\n";
		$php .= " * @link ".$info['link']."\n";
		$php .= " * @package Facebook\n";
		$php .= " */\n";
		$php .= "class ".$info['class']." extends \Sledgehammer\GraphObject {\n";
		$php .= "\n";
		foreach ($info['fields'] as $field) {
			$php .= "\t/**\n";
			$php .= "\t * ".$this->addDot($field['description'])."\n";
			if (count($field['permissions']) !== 0 && $info['fields']['id']['permissions'] !== $field['permissions']) {
				$php .= "\t * Requires ".quoted_human_implode(' or ', $field['permissions'])." permissions.\n";
			}
			if (in_array($field['returns'], array('number', 'string', 'boolean'))) {
				$php .= "\t * @var ".$field['returns']."\n";
			} else {
				$php .= "\t *\n\t * ".$field['returns']."\n";
			}
			$php .= "\t */\n";
			$php .= "\tpublic $".$field['name'].";\n";
			$php .= "\n";
		}
		$typeMapping = array(
			'Achievement(instance)' => 'Achievement',
			'Album' => 'Album',
//			'Account' => Application or Page
			'Event' => 'Event',
			'Message' => 'Message',
			'Order' => 'Order',
			'Page' => 'Page',
			'Photo' => 'Photo',
			'Link' => 'Link',
			'Thread' => 'Thread',
			'User' => 'User',
			// Aliases
			'Friend' => 'User',
			'Book' => 'Page',
			'Movie' => 'Page',
			'Like' => 'Page',
			'Music' => 'Page',
			'Television' => 'Page',
			'Conversation' => 'Message',
			'Conversation' => 'Message',
		);
		$invalidTypes = array(
			'Id', 'The', // Invalid types
			'Activity', 'Interest', 'Setting', // Ondocumented types
			'Account', // Undetermined types
		);
		if (count($info['connections']) != 0) {
			$knownConnections = '';
			foreach ($info['connections'] as $connection) {
				$php .= "\t/**\n";
				$php .= "\t * ".$this->addDot($connection['description'])."\n";
				if (count($connection['permissions']) !== 0 && $info['fields']['id']['permissions'] !== $connection['permissions']) {
					$php .= "\t * Requires ".quoted_human_implode(' or ', $connection['permissions'])." permissions.\n";
				}
				$type = false; // 'GraphObject';
				if (preg_match('/array of ([a-z()]+) objects/', $connection['returns'], $match)) {
					$type = ucfirst($match[1]);
				} elseif (preg_match('/array of objects containing ([a-z()]+) /', $connection['returns'], $match)) {
					$type = ucfirst($match[1]);
				}
				if (isset($typeMapping[$type])) { // Is the detected type valid?
					$type = $typeMapping[$type];
				} elseif ($type) {
					if (in_array($type, $invalidTypes) == false) {
						notice('Unknown type: "'.$type.'"');
					}
					$type = false;
				}
				if ($type) {
					$php .= "\t * @var Collection|".$type."\n";
				} else {
					$php .= "\t *\n\t * Returns ".$connection['returns']."\n";
					$php .= "\t * @var Collection|GraphObject\n";
				}

				$php .= "\t */\n";
				if (isset($info['fields'][$connection['name']])) {
					$php .= '//'; // Prevent "duplicate property" parse error.
				}
				$php .= "\tpublic $".$connection['name'].";\n";
				$php .= "\n";
				$knownConnections .= "\t\t\t'".$connection['name']."' => array(";
				if ($type) {
					$knownConnections .= "'class' => '\\Sledgehammer\Facebook\\".$type."'";
				}
				$knownConnections .= "),\n";
			}
		}
		if ($info['constructor']) {
			$php .= "\t/**\n";
			$php .= "\t * Constructor\n";
			$php .= "\t * @param mixed \$id\n";
			$php .= "\t * @param array \$parameters\n";
			$php .= "\t * @param bool \$preload  true: Fetch fields now. false: Fetch fields when needed.\n";
			$php .= "\t */\n";
			$php .= "\tfunction __construct(\$id, \$parameters = null, \$preload = false) {\n";
			$php .= "\t\tif (\$id === null || is_array(\$id)) {\n";
			$php .= "\t\t\tparent::__construct(\$id, \$parameters, \$preload);\n";
			$php .= "\t\t\treturn;\n";
			$php .= "\t\t}\n";
			$php .= "\t\tif (\$parameters === null) { // Fetch all allowed fields?\n";
			$php .= "\t\t\t\$parameters = array(\n";
			$php .= "\t\t\t\t'fields' => implode(',', \$this->getAllowedFields(array('id' => \$id))),\n";
			$php .= "\t\t\t);\n";
			$php .= "\t\t}\n";
			$php .= "\t\tparent::__construct(\$id, \$parameters, \$preload);\n";
			$php .= "\t}\n";
			$php .= "\n";
		}
		// @todo generate getFieldPermissions()
		if (count($info['connections']) != 0) {
			$php .= "\tprotected static function getKnownConnections(\$options = array()) {\n";
			$php .= "\t\t\$connections = array(\n";
			$php .= $knownConnections;
			$php .= "\t\t);\n";
			// @todo Add permissions based on 'user/friend' option.
			$php .= "\t\treturn \$connections;\n";
			$php .= "\t}\n";
			$php .= "\n";
		}
		$php .= "}\n\n";
		$php .= "?>";
		return $php;
	}

	private function addDot($description) {
		if (text($description)->endsWith('.')) {
			return $description;
		}
		return $description.'.';
	}

}

?>