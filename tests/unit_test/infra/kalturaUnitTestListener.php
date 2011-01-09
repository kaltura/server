<?php

require_once(dirname(__FILE__) . '/../bootstrap.php');

class kalturaUnitTestListener implements PHPUnit_Framework_TestListener
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
		
		if($test instanceof UnitTestBase)
			if($test->currentFailure != null)
			{
				if(kalturaUnitTestListener::$failures == null)
				{
					kalturaUnitTestListener::$failures = new testsFailures();
					kalturaUnitTestListener::$failures->failures[] = new testCaseFailure($test->getInputs());
				}
				
				$currentTestFailures = end(kalturaUnitTestListener::$failures->failures);
				$currentTestFailures->failures[] = $test->currentFailure; 
			}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addIncompleteTest()
	 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addSkippedTest()
	 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTestSuite()
	 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		// TODO Auto-generated method stub
		//unset the failures for the suite back to null
		kalturaUnitTestListener::$failures = new testsFailures();
		kalturaUnitTestListener::$failures->failures[] = new testCaseFailure();

	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTestSuite()
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
		// TODO Auto-generated method stub
		//1. create the failure file for that suite and output there all the failures for the suite
		
		$this->cleanEmptyFailures();
				
		if(kalturaUnitTestListener::$failures != null)
		{
			fwrite(UnitTestBase::$failureObjectsFile, kalturaUnitTestListener::$failures->toXml());
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTest()
	 */
	public function startTest(PHPUnit_Framework_Test $test) {
		// TODO Auto-generated method stub
		//Add another test case failure for this test
		kalturaUnitTestListener::$failures->failures[] = new testCaseFailure($test->getInputs());
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) {
		// when we finish a test we need to see if there were no errors in the test we need to clean him from the failure object
		if(!$test->hasFailures)
		{
			if(kalturaUnitTestListener::$failures->failures != null)
			{
				//remove from result object
				array_pop(kalturaUnitTestListener::$failures->failures);
			}
		}
	}
	
	private function cleanEmptyFailures()
	{
		//for each failure:
		if(kalturaUnitTestListener::$failures !=null)
		{
			foreach (kalturaUnitTestListener::$failures->failures as $key => $testFailureValue)
			{
				//if there were no failures
				if(count($testFailureValue->failures) == 0)
				{
					unset(kalturaUnitTestListener::$failures->failures[$key]);
				}
			}
				
			if(count(kalturaUnitTestListener::$failures->failures) == 0)
			{
				kalturaUnitTestListener::$failures = null;
			}
		}
	}
} 