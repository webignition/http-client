<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/../../lib/bootstrap.php');

$controller = new \webignition\Http\Client\Test\Controller();
$controller->setLibraryPath(__DIR__ . '/../library');
$controller->runTests();