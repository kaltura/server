<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

//Command line usage
if(count($argv) == 2)
{
	$dataFilePath = $argv[0];
	$failuresFilePath = $argv[1];
}
else //Change values when run from Zend Studio
{
	$basePath = "C:/opt/kaltura/app/tests/unitTests/kdl/testsData/";
	$dataFilePath = $basePath."KDLTest.Data";
	$failuresFilePath = $basePath. "KDLTest.failures";
}

KalturaTestResultUpdater::UpdateResults($dataFilePath, $failuresFilePath);

?>