<?php
/**
 * De *.css en *.js in de echte public/ map (apache's documentroot) vullen met de bestanden uit de diverse public/ mappen
 */
namespace Sledgehammer;

echo "\nMinify DocumentRoot\n";
//ini_set('memory_limit', '256M');
require_once(dirname(__FILE__).'/../../core/bootstrap.php');

if ($argc > 1) {
	$folders = array_slice($argv, 1);
} else {
	// Detecteer de publieke folder(s)
	$folders = array();
	$detectFolders = array('www', 'public');
	foreach ($detectFolders as $folder) {
		if (file_exists(PATH.$folder.'/rewrite.php')) {
			$folders[] = $folder;
		}
	}
}
if (count($folders) == 0) {
	echo "  FAILED: No folders detected.\n";
	echo "  Usage: php ".basename(__FILE__)." folder1 [folder2]\n";
	echo "\n";
	return false;
}


if (function_exists('minifyAppendFiles') == false) { // Wordt dit bestand opnieuw geinclude? @todo Eigen function.php bestand?
	/**
	 * Recursieve functie die een map incl. submappen doorzoek naar *.js bestanden
	 *
	 * @return array
	 */
	function minifyAppendFiles( &$files, $path, $targetPrefix, $pathSuffix = '') {
		$dir = new \DirectoryIterator($path.$pathSuffix);
		foreach ($dir as $entry) {
			if (substr($entry->getFilename(), 0, 1) == '.') {
				continue;
			}
			if ($entry->isDir()) {
				minifyAppendFiles($files, $path, $targetPrefix, $pathSuffix.$entry->getFilename().'/');


			} else{
				$extension = strtolower(file_extension($entry->getFilename()));
				if (in_array($extension, array('js', 'css', 'png', 'jpeg', 'jpg'))) {
					$files[$targetPrefix.substr($entry->getPathname(), strlen($path))] = $entry->getPathname(); // Add file (or overrule from app/public/)
				}
			}
		}
		return $files;
	}

	function kib_format($size_in_bytes, $precision = 2, $pad_length = 7) {
		$number = number_format($size_in_bytes / 1024, $precision, '.', '' );
		return str_pad($number, $pad_length, ' ', STR_PAD_LEFT). ' KiB';
	}
}

$modules = Framework::getModules();

$files = array();
// Scanning $module/public/ folders
foreach ($modules as $module => $info) {
	$module_path = $info['path'];
	if (is_dir($module_path.'public')) {
		if (array_value($info, 'app')) {
			$prefix = '';
		} else {
			$prefix = $module.'/';
		}
		minifyAppendFiles($files, $module_path.'public/', $prefix);
	}
}

$minifyCacheFolder = TMP_DIR.'minify/';
//rmdirs($minifyCacheFolder); rmdir($minifyCacheFolder);

$totalFullSize = array(
	'javascript' => 0,
	'css' => 0,
	'images' => 0,
	'total' => 0
);
$totalMinifiedSize = $totalFullSize;

foreach($files as $filename => $pathname) {
	$minifiedPathname = $minifyCacheFolder.$filename;
	$extension = strtolower(file_extension($filename));
	if ($extension === 'css') {
		$type = 'css';
	} elseif ($extension === 'js') {
		$type = 'javascript';
	} else {
		$type = 'images';
	}
	if (file_exists($minifiedPathname) && filemtime($minifiedPathname) > filemtime($pathname)) { // Is het cache bestand up2date?
		echo '  "'.$filename.'" (cached) '; flush();
		$minifiedSize = filesize($minifiedPathname);
		$fullSize = filesize($pathname);
	} else { // Het bestand moet (opnieuw) geminified worden
		echo "  Processing: \"".$filename."\""; flush();
		$script = file_get_contents($pathname);
		$fullSize = strlen($script);
		if ($type === 'css') {
			$minifiedScript = \CssMin::minify($script);
		} elseif ($type === 'javascript') {
			$minifiedScript = \JSMinPlus::minify($script);
		} else {
			$minifiedScript = ImageOptimizer::minify($script, $filename);
		}
		if ($minifiedScript === false) { // Is het minify proces mislukt?
			$minifiedScript = $script; // Gebruik dan het orginele bestand.
		}
		$minifiedSize = strlen($minifiedScript);
		mkdirs(dirname($minifiedPathname).'/');
		if (file_put_contents($minifiedPathname, $minifiedScript) === false) {
			echo "\n  FAILED.\n";
			exit;
		}
	}
	echo "    ".round(($minifiedSize - $fullSize) / 1024, 2).'KiB ('.round((1 - ($minifiedSize / $fullSize)) * 100)."%)\n";
	//echo "    ".round((1 - ($minifiedSize / $fullSize)) * 100).'% ('.round($minifiedSize / 1024, 2).' / '.round($fullSize / 1024, 2)." KiB)\n";
	$totalMinifiedSize[$type] += $minifiedSize;
	$totalFullSize[$type] += $fullSize;
	$totalMinifiedSize['total'] += $minifiedSize;
	$totalFullSize['total'] += $fullSize;
}

echo "\n  Writing: ";
// Kopier de gecomprimeerde bestanden naar de DocumentRoot(s)
foreach ($folders as $folder) {
	echo '"'.$folder.'/" ';
	foreach($files as $filename => $null) {
		$targetFilename = PATH.$folder.'/'.$filename;
		mkdirs(dirname($targetFilename));
		if (!copy($minifyCacheFolder.$filename, $targetFilename)) {
			echo "\n  FAILED.\n";
			exit;
		}
	}
}

echo "\n  Summary\n";
foreach ($totalFullSize as $type => $total) {
	$minified = $totalMinifiedSize[$type];
	echo "    Type: ".$type,"\n";
	echo "      Normal:    ".kib_format($total)."\n";
	echo "      Minified:  ".kib_format($minified)."\n";
	echo "      Reduction: ".kib_format($total - $minified)." (".round((1 - ($minified / $total)) * 100, 1)."%)\n";
}
echo "\n";
return true;
?>
