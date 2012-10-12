<?php
/**
 * HTML5
 */
?><!DOCTYPE html>
<html<?php echo $htmlParameters; ?>>
<head>
	<title><?php echo $title; ?></title>
<?php
foreach ($head as $html) {
	echo "\t".$html."\n";
}
?>
</head>
<?php flush(); ?>
<body<?php echo $bodyParameters; ?>>

<?php render($body); ?>
<?php if ($showStatusbar) { include(__DIR__.'/../statusbar.php'); } ?>

</body>
</html>
