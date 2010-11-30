<?php

/**
 * This script is used in order to generate the different data files for the test.
 * Input: you should change the configFileName.
 * Output: the data generator wll go through the config file and will create the needed dat files. 
 * 
 */

	chdir(dirname(__FILE__));
	require_once ('../bootstrap.php');
	
	$configFileName = "C:/opt/kaltura/app/tests/unit_test/unitTests/KDL/tests_data/test1.config";
	
	$testsDataGenerator = new UnitTestDataGenerator($configFileName);
	$testsDataGenerator->createTestFiles();
?>