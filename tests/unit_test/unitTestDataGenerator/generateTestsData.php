<?php

/**
 * This script is used in order to generate the different data files for the test.
 * Input: you should change the configFileName.
 * Output: the data generator wll go through the config file and will create the needed dat files.
 * 
 */

	chdir(dirname(__FILE__));
	require_once ('../bootstrap.php');
	
	if(count($argv) > 1)
	{
		$configFileName = $argv[1];
	}
	else 
	{
		//KDL tests
//		$configFileName = "C:/opt/kaltura/app/tests/unit_test/unitTests/DKL/tests_data/KDLTests.config";

		//API_V3 tests
		$configFileName = "C:/opt/kaltura/app/tests/unit_test/unitTests/api_v3/mediaService/tests_data/MediaServiceTests.config";
	}
	
	$testsDataGenerator = new UnitTestDataGenerator($configFileName);
	$testsDataGenerator->createTestFiles();
