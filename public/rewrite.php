<?php
/**
 * rewrite.php
 */

use App\App;
use Sledgehammer\Core\Debug\ErrorHandler;

define('Sledgehammer\STARTED', microtime(true));
include(dirname(__FILE__).'/../vendor/sledgehammer/core/src/render_public_folders.php');
require(dirname(__FILE__).'/../vendor/autoload.php');

ErrorHandler::enable();
$app = new App();
$app->handleRequest();
