<?php
/**
 * Works like "core/render_public_folder.php" but will minify *.css, *.js and image files.
 * @package Minify
 */
namespace Sledgehammer;

if (!defined('Sledgehammer\STARTED')) {
	define('Sledgehammer\STARTED', microtime(true));
}

$webpath = dirname((isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME']));
if ($webpath != '/') {
	$webpath .= '/';
}
$uriPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); // Het path gedeelte van de uri
$relativeWebpath = substr($uriPath, strlen($webpath)); // Bestandsnaam is het gedeelte van de uriPath zonder de WEBPATH
$modulePath = dirname(dirname(__FILE__));
$files = array(
	dirname($modulePath).'/application/public/'.$relativeWebpath,
);

$firstSlashPos = strpos($relativeWebpath, '/');
if ($firstSlashPos) { // Gaat het om een submap?
	$firstSlashPos++;
	$firstFolder = substr($relativeWebpath, 0, $firstSlashPos);
	$filepath = substr($relativeWebpath, $firstSlashPos);
	$files[] = $modulePath.'/'.$firstFolder.'public/'.$filepath; // Dan kan het bestand ook in een module staan
}
if ($relativeWebpath == '' || substr($relativeWebpath, -1) == '/') { // Gaat de request om een map?
	$indexFiles = array();
	$files[] = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$relativeWebpath;
	foreach ($files as $filename) {
		// Zoek naar index bestanden in de public/ mappen. Ala DirectoryIndex
		foreach(array('index.html', 'index.htm', 'index.php') as $indexFile) {
			$indexFiles[] = $filename.$indexFile;
		}
	}
	$files = $indexFiles;
}
foreach($files as $filename) {
	if (is_readable($filename)) {
		if (substr($filename, -4) == '.php') { // Is het een php bestand?
			if ($_SERVER['SCRIPT_FILENAME'] == $filename) { // Is het het php-bestand waar de mod_rewrite heen gaat?
					break; // Deze niet includen, dit php-bestand is al actief en include juist dit bestand. (infinite loop)
			}
			chdir(dirname($filename));
			include($filename); // Include het php bestand.
			exit;
		}
		require_once($modulePath.'/core/functions.php');
		if (is_dir($filename)) {
			error_log('Requesting a public folder without a trailing slash, redirecting to "'.$uriPath.'/"', E_NOTICE);
			redirect($uriPath.'/'); //	Redirect naar dezelfde url, maar dan als mapnaam
		}
		$extension = strtolower(file_extension($filename));
		if (in_array($extension, array('js', 'css', 'png', 'jpeg', 'jpg'))) {
			$minifiedPathname = TMP_DIR.'minify'.substr($filename, strlen($modulePath));
			if (file_exists($minifiedPathname) == false || filemtime($minifiedPathname) < filemtime($filename)) { // Is het cache bestand niet up2date?
				require_once($modulePath.'/core/bootstrap.php');
				if ($extension === 'js') {
					$minified = \JSMinPlus::minify(file_get_contents($filename), $filename);
				} elseif ($extension === 'css') {
					$minified = \CssMin::minify(file_get_contents($filename));
				} else {
					$minified = ImageOptimizer::minify(file_get_contents($filename), $filename);
				}
				mkdirs(dirname($minifiedPathname));
				if ($minified === false) { // Minify failed?
					copy($filename, $minifiedPathname); // Use full version
				} else {
					file_put_contents($minifiedPathname, $minified);
				}
			}
			render_file($minifiedPathname);
		}
		render_file($filename); // Render het gewone bestand.
	}
}

define('Sledgehammer\WEBPATH', $webpath);
$folderCount = preg_match_all('/[^\/]+\//', substr($uriPath, strlen(WEBPATH)), $match);
define('Sledgehammer\WEBROOT', str_repeat('../', $folderCount));
//unset($urlPath, $publicFile, $fullpath, $folderCount, $math, $folders, $folder, $files, $filename);
return true;
?>
