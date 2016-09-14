<?php
/**
 * Detect and include the DevUtils installation.
 */
define('Sledgehammer\STARTED', microtime(true));
// Detecteer de locatie van de devutils bestanden.
$locations = array(
	dirname(__DIR__).'/', // Is installed in full
	dirname(dirname(__DIR__)).'/devutils/', // In $project/
	dirname(dirname(dirname(__DIR__))).'/devutils/', // In a  $project/public
	dirname(dirname(dirname(dirname(__DIR__)))).'/devutils/', // in a $project/app/webroot
	'/var/www/devutils/',
);
$devutilsPath = false;
foreach ($locations as $path) {
	if (file_exists($path.'devutils.php')) {
		$devutilsPath = $path;
		break;
	}
}
if ($devutilsPath == false) {
	error_log('DevUtils not found in "'.implode('", "', $locations).'"', E_USER_WARNING);
	die('Error: DevUtils installation not found.');
}
$devutilsIncludes = [
	// Define your bootstrap file(s) here.
];
if (file_exists(__DIR__.'/../../vendor/autoload.php')) { // check for a "vendor/autoload.php".
	$devutilsIncludes[] = realpath(__DIR__.'/../../vendor/autoload.php'); // comment this line it conflict with your bootstrap file(s).
}
require($devutilsPath.'devutils.php');