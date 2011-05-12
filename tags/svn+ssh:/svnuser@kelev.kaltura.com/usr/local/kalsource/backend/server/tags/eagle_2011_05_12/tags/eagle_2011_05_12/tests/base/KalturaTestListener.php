<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents the kaltura test listener that is attached to phpunit
 * @author Roni
 *
 */
class KalturaTestListener implements PHPUnit_Framework_TestListener
{
	/**
	 * All the failures gathered by the listener
	 * @var KalturaTestCaseFailures
	 */
	private static $testCaseFailures;

	/**
	 * 
	 * Holds the file for the failures to be written to
	 * @var unknown_type
	 */
	private static $failuresFile = null;
	
	/**
	 * 
	 * The current test case the listener is working on
	 * This is identified by the class path 
	 * @var static
	 */
	private static  $currentTestCase = null;
	
	/**
	 * @return the $testCaseFailures
	 */
	public static function getTestCaseFailures() {
		return KalturaTestListener::$testCaseFailures;
	}

	/**
	 * @return the $failuresFile
	 */
	public static function getFailuresFile() {
		return KalturaTestListener::$failuresFile;
	}

	/**
	 * @return the $currentTestCase
	 */
	public static function getCurrentTestCase() {
		return KalturaTestListener::$currentTestCase;
	}

	/**
	 * @param KalturaTestCaseFailures $testCaseFailures
	 */
	public static function setTestCaseFailures($testCaseFailures) {
		KalturaTestListener::$testCaseFailures = $testCaseFailures;
	}

	/**
	 * @param unknown_type $failuresFile
	 */
	public static function setFailuresFile($failuresFile) {
		KalturaTestListener::$failuresFile = $failuresFile;
	}

	/**
	 * @param static $currentTestCase
	 */
	public static function setCurrentTestCase($currentTestCase) {
		KalturaTestListener::$currentTestCase = $currentTestCase;
	}

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
		if($test instanceof KalturaTestCaseBase)
		{
			if($test->getCurrentFailure() != null)
			{
				$testProcedureFailures = KalturaTestListener::$testCaseFailures->getTestProceduresFailures();
				
				//If the test procedure failure wasn't added (first use)
				if(count($testProcedureFailures) == 0)
				{
					//Then add the test procedure failure
					KalturaTestListener::$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure($test->getName(false)));
					$testProcedureFailures = KalturaTestListener::$testCaseFailures->getTestProceduresFailures();
				}
				
				//Get the current test procedure
				$currentTestProcedureFailures = end($testProcedureFailures);
				$testCaseInstancesFailures = $currentTestProcedureFailures->getTestCaseInstanceFailures();
					
				if(count($testCaseInstancesFailures) == 0)
				{
					$currentTestProcedureFailures->addTestCaseInstanceFailure(new KalturaTestCaseInstanceFailure($test->getName(true), $test->getInputs()));
					$testCaseInstancesFailures = $currentTestProcedureFailures->getTestCaseInstanceFailures(); 
				}
								
				$currentTestCaseInstanceFailures = end($testCaseInstancesFailures);
				$currentTestCaseInstanceFailures->addFailure($test->getCurrentFailure());
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
		print("In startTestSuite - for suite = {$suite->getName()}\n");
		
		if (preg_match("*::*" ,$suite->getName()) != 0)
		{ 
			//Get the test procedure name from the suite name which is (testCase::testProcedure)
			$testNames = explode("::", $suite->getName());
			$testName = $testNames[0];
			if(isset($testNames[1]))
			{
				$testName = $testNames[1]; 
			}
			
			// if it is a dataprovider test suite
			KalturaTestListener::$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure($testName));
		}
		else 
		{
			//Check if the test belongs to the same test case failures (by the first test of the suite)
			$class = get_class($suite->testAt(0));
				
			//if the new test comes from a new file (testCase)
			if(KalturaTestListener::$currentTestCase != $class)
			{
				//Gets the class path for the failure file
				$classPath = KAutoloader::getClassFilePath($class);
				
				$this->writeFailuresToFile();
				
				//Opens the new failure file for the new test
				KalturaTestListener::$failuresFile = fopen(dirname($classPath) . "/testsData/{$class}.failures", "w+");
	
				//Change the current test case
				KalturaTestListener::$currentTestCase = $class;
				
				//Create new test case failures for the new test case
				KalturaTestListener::$testCaseFailures = new KalturaTestCaseFailures(KalturaTestListener::$currentTestCase);
			
				//TODO: get the test procedure name from the suite
				KalturaTestListener::$testCaseFailures->setTestProceduresFailures(array(new KalturaTestProcedureFailure("Unknown")));
			}
			else
			{
				
			}
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTestSuite()
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) 
	{
		print("\nIn endTestSuite\n");
		if (preg_match("*::*" ,$suite->getName()) != 0)
		{ // if it is a dataprovider test suite
			print("A data provider test suite was finished no action taken\n");
		}
		else //real test suite
		{
			//1. create the failure file for that suite and output there all the failures for the suite
			$this->cleanEmptyFailures();
	
			$this->writeFailuresToFile();
	
			//Zero the failures
			KalturaTestListener::$testCaseFailures = null;
			
			if(KalturaTestListener::$failuresFile)
			{
				fclose(KalturaTestListener::$failuresFile);
				KalturaTestListener::$failuresFile = null;
			}
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTest()
	 */
	public function startTest(PHPUnit_Framework_Test $test) 
	{
		print("In startTest\n");

		if($test instanceof KalturaTestCaseBase)
		{
			//Add another test case instance failure for this test
			$testProceduresFailures = KalturaTestListener::$testCaseFailures->getTestProceduresFailures();
			
			if(count($testProceduresFailures) == 0)
			{
				//Handle when test name includes the test case name
				KalturaTestListener::$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure($test->getName(false)));
				$testProceduresFailures = KalturaTestListener::$testCaseFailures->getTestProceduresFailures();
			}
						
			$currentTestProcedureFailures = end($testProceduresFailures);
			$currentTestProcedureFailures->addTestCaseInstanceFailure(new KalturaTestCaseInstanceFailure($test->getName(true), $test->getInputs()));
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) 
	{
		print("In endTest\n");

		if($test instanceof KalturaTestCaseBase)
		{
			// When we finish a test we need to see if there were no errors
			if(!$test->getHasFailures())
			{
				if(KalturaTestListener::$testCaseFailures != null)
				{
					$testProceduresFailures = KalturaTestListener::$testCaseFailures->getTestProceduresFailures();
					
					$testProcedureFailures = end($testProceduresFailures);
					
					$testCaseInstnaceFailures = $testProcedureFailures->getTestCaseInstanceFailures();
					
					end($testCaseInstnaceFailures);
					$testCaseInstanceFailureKey = key($testCaseInstnaceFailures);
					
					//Clean the test failures from the procedures failures
					$testProcedureFailures->removeTestCaseInstanceFailure($testCaseInstanceFailureKey);
				}
			}
		}
	}
	
	/**
	 * 
	 * Flushes all the listener data to the failure file
	 */
	public function flush()
	{
		$this->cleanEmptyFailures();
		$this->writeFailuresToFile();
	}
	 
	/**
	 * 
	 * Cleans all empty failures that were accidently inserted
	 */
	private function cleanEmptyFailures()
	{
		//for each failure:
		if(KalturaTestListener::$testCaseFailures !=null)
		{
			foreach (KalturaTestListener::$testCaseFailures->getTestProceduresFailures() as $testProcedureKey => $testProcedureFailure)
			{
				foreach ($testProcedureFailure->getTestCaseInstanceFailures() as $testCaseInstanceKey => $testCaseInstanceFailure)
				{
					//if there were no failures
					if(count($testCaseInstanceFailure->getFailures()) == 0)
					{
						//delete the test case instance failure
						$testProcedureFailure->removeTestCaseInstanceFailure($testCaseInstanceKey);					
					}
				}
				
				$cleanCasesFailures = $testProcedureFailure->getTestCaseInstanceFailures();
				if(count($cleanCasesFailures) == 0)
				{
					KalturaTestListener::$testCaseFailures->removeTestProcedureFailure($testProcedureKey);
				}
			}
		}
	}

	/**
	 * 
	 * Sets the listeners failures file path
	 * @param string $failureFilePath
	 */
	public static function setFailureFilePath($failureFilePath)
	{
		KalturaTestListener::$failuresFile = fopen($failureFilePath, 'w+');
	}

	/**
	 * 
	 * Writes the listener failures to the given file
	 */
	private function writeFailuresToFile()
	{
		if(KalturaTestListener::$testCaseFailures != null)
		{
			$testCaseFailuresXml = KalturaTestCaseFailures::toXml(KalturaTestListener::$testCaseFailures, "TestCaseFailures");
			
			if($testCaseFailuresXml != null)
			{
				$testCaseFailuresXml->formatOutput = true;
				fwrite(KalturaTestListener::$failuresFile, $testCaseFailuresXml->saveXML());
			}
			else
			{
				print("failures XML is null!!!\n");
				var_dump($testCaseFailuresXml);
			}
		}
	}
}