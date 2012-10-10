<?php
/**
 * GeoIPLookupUtil
 */
namespace Sledgehammer;
/**
 * @package GeoIP
 */
class GeoIPLookupUtil extends Util {

	/**
	 * Constuctor
	 */
	function __construct() {
		parent::__construct('Geolocation for IP', 'module_icons/geoip.png');
	}

	function generateContent() {
		if (isset($_GET['ip'])) {
			Framework::$autoLoader->importFolder(dirname($this->paths['utils']).'/classes');
			$geoip = new GeoIP();
			$result = $geoip->getCountry(value($_GET['ip']));
			if ($result) {
				return Alert::success('<h4>Maxmind GeoIP</h4>IP: <b>'.$_GET['ip'].'</b> is located in <b>'.$result['country'].'</b> ('.$result['code'].')');
			} else {
				return Alert::error('<h4>Maxmind GeoIP</h4>IP: <b>'.$_GET['ip'].'</b> is not found in the country database.');
			}
		} else {
			return new Form(array(
				'method' => 'get',
				'fields' => array(
					new Input(array('name' => 'ip', 'placeholder' => 'IP address')),
					new Input(array('type' => 'submit', 'value' => 'Lookup', 'class' => 'btn btn-primary')),
				),
				'class' =>'input-append'
			));
		}
	}
}

?>
