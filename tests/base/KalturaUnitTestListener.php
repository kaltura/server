<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrap.php');

class KalturaUnitTestListener implements PHPUnit_Framework_TestListener
{
	/**
	 * All the failures gathered by the listener
	 */
	public static $failures;
	
	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addError()
	 */
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addFailure()
	 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
		
		if($test instanceof KalturaUnitTestCase)
			if($test->currentFailure != null)
			{
				if(KalturaUnitTestListener::$failures == null)
				{
					KalturaUnitTestListener::$failures = new KalturaTestsFailures();
					KalturaUnitTestListener::$failures->failures[] = new KalturaUnitTestCaseFailure($test->getInputs());
				}
				
				$currentTestFailures = end(KalturaUnitTestListener::$failures->failures);
				$currentTestFailures->failures[] = $test->currentFailure; 
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
		KalturaUnitTestListener::$failures = new KalturaTestsFailures();
		KalturaUnitTestListener::$failures->failures[] = new KalturaUnitTestCaseFailure();
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTestSuite()
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
		print("In endTestSuite\n");
		
		//1. create the failure file for that suite and output there all the failures for the suite
		$this->cleanEmptyFailures();
				
		if(KalturaUnitTestListener::$failures != null)
		{
			fwrite(KalturaUnitTestCase::$failureObjectsFile, KalturaUnitTestListener::$failures->toXml());
		}
		
		//Zero the failures
		KalturaUnitTestListener::$failures = null;
		
		if(KalturaUnitTestCase::$failureObjectsFile)
		{
			fclose(KalturaUnitTestCase::$failureObjectsFile);
			KalturaUnitTestCase::$failureObjectsFile = null;
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTest()
	 */
	public function startTest(PHPUnit_Framework_Test $test) {
		print("In startTest\n");

		if($test instanceof KalturaUnitTestCase)
		{
			//Add another test case failure for this test
			KalturaUnitTestListener::$failures->failures[] = new KalturaUnitTestCaseFailure($test->getInputs());
	
			if(KalturaUnitTestCase::$failureObjectsFile == null)
			{
				//Opens the new file for the new test
				$class = get_class($test);
				$classPath = KAutoloader::getClassFilePath($class);
				KalturaUnitTestCase::$failureObjectsFile = fopen(dirname($classPath) . "/testsData/{$test->getName(false)}.failures", "w+");
			}
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) {
		print("In endTest\n");

		if($test instanceof KalturaUnitTestCase)
		{
			// When we finish a test we need to see if there were no errors in the test we need to clean him from the failure object
			if(!$test->hasFailures)
			{
				if(KalturaUnitTestListener::$failures != null)
				{
					if(KalturaUnitTestListener::$failures->failures != null)
					{
						//Remove from result object
						array_pop(KalturaUnitTestListener::$failures->failures);
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
		if(KalturaUnitTestListener::$failures !=null)
		{
			foreach (KalturaUnitTestListener::$failures->failures as $key => $testFailureValue)
			{
				//if there were no failures
				if(count($testFailureValue->failures) == 0)
				{
					unset(KalturaUnitTestListener::$failures->failures[$key]);
				}
			}
				
			if(count(KalturaUnitTestListener::$failures->failures) == 0)
			{
				KalturaUnitTestListener::$failures = null;
			}
		}
	}
} 