<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents the kaltura test listener that is attached to phpunit
 * @author Roni
 *
 */
class KalturaUnitTestListener implements PHPUnit_Framework_TestListener
{
	/**
	 * All the failures gathered by the listener
	 * @var KalturaTestFailures
	 */
	public static $testFailures;

	/**
	 * 
	 * Holds the file for the failures to be written to
	 * @var unknown_type
	 */
	public static $failuresFile = null;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addError()
	 */
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addFailure()
	 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) 
	{
		if($test instanceof KalturaUnitTestCase)
		{
			if($test->currentFailure != null)
			{
				if(KalturaUnitTestListener::$testFailures == null)
				{
					KalturaUnitTestListener::$testFailures = new KalturaTestFailures();
					KalturaUnitTestListener::$testFailures->testCaseFailures[] = new KalturaTestCaseFailure($test->getInputs());
				}
				
				$currentTestFailures = end(KalturaUnitTestListener::$testFailures->testCaseFailures);
				$currentTestFailures->failures[] = $test->currentFailure; 
			}
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addIncompleteTest()
	 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		print("In addIncompleteTest\n");
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addSkippedTest()
	 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		print("In addSkippedTest\n");
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTestSuite()
	 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		print("In startTestSuite\n");
		//unset the failures for the suite back to null
		KalturaUnitTestListener::$testFailures = new KalturaTestFailures();
		KalturaUnitTestListener::$testFailures->testCaseFailures[] = new KalturaTestCaseFailure();
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTestSuite()
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) 
	{
		print("In endTestSuite\n");
		
		//1. create the failure file for that suite and output there all the failures for the suite
		$this->cleanEmptyFailures();
				
		if(KalturaUnitTestListener::$testFailures != null)
		{
			fwrite(KalturaUnitTestListener::$failuresFile, KalturaUnitTestListener::$testFailures->toXml());
		}
		
		//Zero the failures
		KalturaUnitTestListener::$testFailures = null;
		
		if(KalturaUnitTestListener::$failuresFile)
		{
			fclose(KalturaUnitTestListener::$failuresFile);
			KalturaUnitTestListener::$failuresFile = null;
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTest()
	 */
	public function startTest(PHPUnit_Framework_Test $test) 
	{
		print("In startTest\n");

		if($test instanceof KalturaUnitTestCase)
		{
			//Add another test case failure for this test
			KalturaUnitTestListener::$testFailures->testCaseFailures[] = new KalturaTestCaseFailure($test->getInputs());
	
			if(KalturaUnitTestListener::$failuresFile == null)
			{
				//Opens the new file for the new test
				$class = get_class($test);
				$classPath = KAutoloader::getClassFilePath($class);
				KalturaUnitTestListener::$failuresFile = fopen(dirname($classPath) . "/testsData/{$test->getName(false)}.failures", "w+");
			}
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) 
	{
		print("In endTest\n");

		if($test instanceof KalturaUnitTestCase)
		{
			// When we finish a test we need to see if there were no errors in the test we need to clean him from the failure object
			if(!$test->hasFailures)
			{
				if(KalturaUnitTestListener::$testFailures != null)
				{
					if(KalturaUnitTestListener::$testFailures->testCaseFailures != null)
					{
						//Remove from result object
						array_pop(KalturaUnitTestListener::$testFailures->testCaseFailures);
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * Cleans all empty failures that were accidently inserted
	 */
	private function cleanEmptyFailures()
	{
		//for each failure:
		if(KalturaUnitTestListener::$testFailures !=null)
		{
			foreach (KalturaUnitTestListener::$testFailures->testCaseFailures as $key => $testFailureValue)
			{
				//if there were no failures
				if(count($testFailureValue->failures) == 0)
				{
					unset(KalturaUnitTestListener::$testFailures->testCaseFailures[$key]);
				}
			}
				
			if(count(KalturaUnitTestListener::$testFailures->testCaseFailures) == 0)
			{
				KalturaUnitTestListener::$testFailures = null;
			}
		}
	}
} 