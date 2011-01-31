<?php

/**
 * This script is used in order to generate the different data files for the test.
 * Input: you should change the configFileName.
 * Output: the data generator wll go through the config file and will create the needed dat files.
 * 
 */

	require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');
	
	if(count($argv) > 1)
	{
		$configFileName = $argv[1];
	}
	else //TODO: clean the else segmant
	{
		//R&P tests
		$configFileName = "C:/opt/kaltura/app/tests/roles_and_permissions/testsData/testPermissionServiceTest.config";
	}
	
	$testsDataGenerator = new KalturaUnitTestDataGenerator($configFileName);
	$testsDataGenerator->createTestFiles();