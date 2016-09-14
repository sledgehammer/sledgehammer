<?php
/**
 * Example Template
 */
?>
<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="<?= Sledgehammer\WEBROOT; ?>index.html">My App</a>
	</div>
</div>
<div class="container">
<?php render($content); ?>
</div>