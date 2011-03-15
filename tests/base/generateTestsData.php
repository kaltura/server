<?php

/**
 * This script is used in order to generate the different data files for the test.
 * Input: you should change the configFileName.
 * Output: the data generator wll go through the config file and will create the needed dat files.
 * 
 */

	require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');
	
	//When used from command line
	if(count($argv) > 1)
	{
		$configFileName = $argv[1];
	}
	else //Change values if you use from the Zend Studio
	{
		//KDL tests
		$configFileName = "C:/opt/kaltura/app/tests/unitTests/kdl/testsData/KDLTest.config";
	}
	
	$testsDataGenerator = new KalturaTestDataGenerator($configFileName);
	$testsDataGenerator->createTestDataFiles();