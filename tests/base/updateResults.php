<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

//Command line usage 
//TODO: check and add support
if(count($argv) == 2)
{
	$dataFilePath = $argv[0];
	$failuresFilePath = $argv[1];
}
else
{
	$basePath = "C:/opt/kaltura/app/tests/unit_test/unitTests/KDL/testsData/";
	$dataFilePath = $basePath."RealTest1.Data";
	$failuresFilePath = $basePath. "KDLUnitTest.failures";
}

unitTestResultUpdater::UpdateResults($dataFilePath, $failuresFilePath);

?>