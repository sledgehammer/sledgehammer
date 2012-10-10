<?php
/**
 * Update the GeoIP database to the latest version from maxmind.com
 */
namespace Sledgehammer;
require(dirname(__FILE__).'/../../core/bootstrap.php');

$VERBOSE = false; // true: Include human readable IP addresses into the geoip database

echo "\nUpdating GeoIP database\n";

$dbFile = TMP_DIR.'GeoIP/update.sqlite';
if (file_exists($dbFile)) {
	unlink($dbFile);
}

// Download
echo "  Downloading...";
mkdirs(TMP_DIR.'GeoIP');
$zipFile = TMP_DIR.'GeoIP/CountryCSV.zip';
if (file_exists($zipFile) == false || filemtime($zipFile) < (time() - 3600)) { // Is het gedownloade bestand ouder dan 1 uur?
	cURL::download('http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip', $zipFile);
	echo " done\n"; flush();
} else {
	echo " skipped\n";
}

// Unzip
$csvFile = TMP_DIR.'GeoIP/GeoIPCountryWhois.csv';
if (file_exists($csvFile)) {
	unlink($csvFile);
}
echo "  Extracting...";

$archive = new \ZipArchive();
if ($archive->open($zipFile) !== true) {
	throw new \Exception('Failed to open zipfile');
}
$archive->extractTo(TMP_DIR.'GeoIP/');
echo " done\n";

// Rebuild
echo "  Creating database...";
$db = new Database('sqlite:'.$dbFile);
Database::$instances['GeoIP Import'] = $db;
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // end script on a sql error

$db->query('CREATE TABLE country (
	code CHAR(2) PRIMARY KEY,
	name VARCHAR(150) NOT NULL
)');
if ($VERBOSE) {
	$db->query('CREATE TABLE ip2country (
		begin  UNSIGNED INTEGER PRIMARY KEY,
		end    UNSIGNED INTEGER NOT NULL,
		ip_begin TEXT NOT NULL,
		ip_end TEXT NOT NULL,
		country_code CHAR(2) NOT NULL REFERENCES country(code)
	)');
} else {
	$db->query('CREATE TABLE ip2country (
		begin  UNSIGNED INTEGER PRIMARY KEY,
		end    UNSIGNED INTEGER NOT NULL,
		country_code CHAR(2) NOT NULL REFERENCES country(code)
	)');
}
$db->query('CREATE INDEX end_ix ON ip2country (end)');

// Kolomnamen toevoegen
//ini_set('memory_limit', '128M');
file_put_contents($csvFile, "begin_ip,end_ip,begin_num,end_num,code,country\n".file_get_contents($csvFile));

// Eerst de countries importeren
$csv = new CSV($csvFile, null, ',');
$countries = array();
$rowCount = 0;
foreach($csv as $row) {
	$countries[$row['code']] = $row['country'];
	$rowCount++;
}
$db->beginTransaction();
foreach ($countries as $code => $country) {
	$db->query('INSERT INTO country (code, name) VALUES ('.$db->quote($code).', '.$db->quote($country).')');
}
echo " done.\n";
echo "  Importing data (";
// Daarna alle ip-ranges importeren.
echo $rowCount." records)\n    ";
$previousTs = microtime(true);
if ($VERBOSE) {
	$statement = $db->prepare('INSERT INTO ip2country (begin, end, ip_begin, ip_end, country_code) VALUES (:begin, :end, :ip_begin, :ip_end, :country_code)');
} else {
	$statement = $db->prepare('INSERT INTO ip2country (begin, end, country_code) VALUES (:begin, :end, :country_code)');
}
foreach($csv as $index => $row) {
	$now = microtime(true);
	if ($previousTs < ($now - 1)) {
		echo round(($index / $rowCount) * 100), '% '; flush();
		$previousTs = $now;
	}
	$params = array(
		'begin'=> $row['begin_num'],
		'end'=> $row['end_num'],
		'country_code' => $row['code']
	);
	if ($VERBOSE) {
		$params['ip_begin'] = $row['begin_ip'];
		$params['ip_end'] = $row['end_ip'];
	}
	$statement->execute($params);

}
$db->commit();
echo " done\n  Upgrading files...";
copy($dbFile, TMP_DIR.'GeoIP/countries.sqlite');
$filename = dirname(__FILE__).'/../data/countries.sqlite';
if (copy($dbFile, $filename)) {
	echo " done.\n";
} else {
	echo " FAILED.\n";
}

?>