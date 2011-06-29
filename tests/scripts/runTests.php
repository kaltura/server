<?php

print ("Usage: php runTests.php [TestDirName]\n");
print ("If no Test name is given then all tests are ran\n");

$testName = null; 
if(isset($argv[1]))
	$testName = $argv[1];

define('API_TEST_DIR', "/opt/kaltura/app/tests/api");

$apiTestDir = opendir(API_TEST_DIR);
if ($apiTestDir) // Checks if the test API folder is a dir 
{
    echo "API Tests Directory handle: " . API_TEST_DIR . "\n";

    /* loop over the directory. */
    while (false !== ($serviceDirName = readdir($apiTestDir))) 
    {
    	$isDir = is_dir(API_TEST_DIR . "/" . $serviceDirName);
        
        if($isDir) // Checks if the current service dir is a dir 
        {
        	$isRunServiceTest = (is_null($testName) || $testName == $serviceDirName);
        	if($isRunServiceTest)
        	{
	        	print("Testing service: $serviceDirName\n");
	        	$serviceDir = opendir(API_TEST_DIR . "/" . $serviceDirName);
	        	
	        	if($serviceDir)
	        	{
		        	while (false !== ($testFile = readdir($serviceDir))) //Reads all files from the service dir 
		        	{
		        		$testFile = API_TEST_DIR . "/" . $serviceDirName . "/" . $testFile;
		        		$isFile = is_file($testFile);
		        		$isIni = substr_count($testFile, "Test.php.ini");
		        		$isTest = substr_count($testFile, "Test.php");
		        				        		
		        		if($isFile && $isTest && !$isIni) //Run only the test file
		        		{
		        			print("Running test: $testFile!\n");
		        			$output = array();
		        			$result = exec("phpunit " . $testFile, $output);
		        			print("output: " . print_r($output, true ) ."\n");
		        			print("result: " . print_r($result, true ) ."\n\n\n");
		        		}
		        	}
	        	}
        	}
    	}
    }
}
else 
{
	print("Test Dir [" . API_TEST_DIR . "] is not a dir\n");
}
    
print("\nRunning API tests ended!\n");