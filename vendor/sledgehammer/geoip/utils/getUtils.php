<?php
namespace Sledgehammer;
return array(
	'lookup.php' => new GeoIPLookupUtil(),
	'update_db.php' => new UtilScript('update_db.php', 'Update GeoIP database'),
);
?>
