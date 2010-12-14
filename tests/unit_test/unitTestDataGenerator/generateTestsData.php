<?php

/**
 * This script is used in order to generate the different data files for the test.
 * Input: you should change the configFileName.
 * Output: the data generator wll go through the config file and will create the needed dat files.
 * 
 */

	require_once (dirname(__FILE__) . '/../bootstrap.php');
	
	if(count($argv) > 1)
	{
		$configFileName = $argv[1];
	}
	else //TODO: clean the else segmant
	{
//		//KDL tests
//		$configFileName = "C:/opt/kaltura/app/tests/unit_test/unitTests/KDL/tests_data/KDLTests.config";
//
//		$testsDataGenerator = new UnitTestDataGenerator($configFileName);
//		$testsDataGenerator->createTestFiles();
//	
		//API_V3 tests
		$configFileName = "C:/opt/kaltura/app/tests/unit_test/unitTests/api_v3/mediaService/tests_data/MediaServiceTests.config";

		$testsDataGenerator = new UnitTestDataGenerator($configFileName);
		$testsDataGenerator->createTestFiles();
		
//		//Example Tests
//		$configFileName = "C:/opt/kaltura/app/tests/unit_test/unitTests/unitTestExample/tests_data/ExampleTestConfigFile.config";
//		
//		$testsDataGenerator = new UnitTestDataGenerator($configFileName);
//		$testsDataGenerator->createTestFiles();
	}
	
	$testsDataGenerator = new UnitTestDataGenerator($configFileName);
	$testsDataGenerator->createTestFiles();

