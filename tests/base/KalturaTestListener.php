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
	 * The current data file
	 * @var string
	 */
	private static $failureFilePath = null;
	
	/**
	 * 
	 * The current data file
	 * @var string
	 */
	private static $dataFilePath = null;
	
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
			$currentFailure = $test->getCurrentFailure();
			if($currentFailure != null)
			{
				$testProcedureName = $test->getName(false);
				$testProcedureFailure = KalturaTestListener::$testCaseFailures->getTestProcedureFailure($testProcedureName);
				
				//If the test procedure failure wasn't added (first use)
				if(is_null($testProcedureFailure))
				{
					//Then add the test procedure failure
					$testProcedureFailure = KalturaTestListener::$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure($testProcedureName));
				}
				
				$testCaseInstanceName = $test->getName(true);
				$testCaseInstanceFailures = $testProcedureFailure->getTestCaseInstanceFailure($testCaseInstanceName);
				
				if(is_null($testCaseInstanceFailures))
				{
					$testCaseInstanceFailures = $testProcedureFailure->addTestCaseInstanceFailure(new KalturaTestCaseInstanceFailure($test->getName(true), $test->getInputs()));
				}
								
				$testCaseInstanceFailures->addFailure($currentFailure);
			}
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addIncompleteTest()
	 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		print("In addIncompleteTest\n");
		KalturaLog::debug("In addIncompleteTest");
		
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::addSkippedTest()
	 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
		$message = $e->getMessage();
		$testName = $test->getName(); 
		print("In addSkippedTest, testName [$testName], message [$message]\n");
		KalturaLog::debug("In addSkippedTest");
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::startTestSuite()
	 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		print("In startTestSuite - for suite = {$suite->getName()}\n");
		KalturaLog::debug("In startTestSuite - for suite = {$suite->getName()}");

		//TODO: fix this to use the data provider type check
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
				
				KalturaTestListener::$failureFilePath = dirname($classPath) . "/testsData/{$class}.failures";
				KalturaTestListener::$dataFilePath = dirname($classPath) . "/testsData/{$class}.data";
				
				$this->writeFailuresToFile();
				
				//Opens the new failure file for the new test
				KalturaTestListener::$failuresFile = fopen(KalturaTestListener::$failureFilePath, "w+");
	
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
		$suiteName = $suite->getName();
		print("\nIn endTestSuite for suite [$suiteName]\n");
		KalturaLog::debug("In endTestSuite");

		//if (preg_match("*::*" ,$suiteName) != 0) TODO: check this
		if($suite instanceof  PHPUnit_Framework_TestSuite_DataProvider)
		{ // if it is a dataprovider test suite
			print("A data provider test suite was finished no action taken\n");
			KalturaLog::debug("A data provider test suite was finished no action taken");

			//TODO: add here logic for multi nested tests
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
		$testName = $test->getName();
		print("In startTest for test $testName\n");
				
		KalturaLog::debug("In startTest for test $testName");

		if($test instanceof KalturaTestCaseBase)
		{
			$testInputs = $test->getInputs();
			
			//Add another test case instance failure for this test
			$testProcedureName = $test->getName(false); 
			$testProcedureFailures = KalturaTestListener::$testCaseFailures->getTestProcedureFailure($testProcedureName);
			
			if(is_null($testProcedureFailures))
			{
				//Handle when test name includes the test case name
				$testProcedureFailures = KalturaTestListener::$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure());
			}
	
			$testProcedureFailures->addTestCaseInstanceFailure(new KalturaTestCaseInstanceFailure($test->getName(true), $test->getInputs()));
		}
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestListener::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time) 
	{
		print("In endTest\n");
		KalturaLog::debug("In endTest");

		if($test instanceof KalturaTestCaseBase)
		{
			// When we finish a test we need to see if there were no errors
			if(!$test->getHasFailures())
			{
				if(KalturaTestListener::$testCaseFailures != null)
				{
					$testProcedureName = $test->getName(false);
					$testProcedureFailures = KalturaTestListener::$testCaseFailures->getTestProcedureFailure($testProcedureName);

					$testCaseInstanceName = $test->getName(true);
				
					//Clean the test failures from the procedures failures
					$testProcedureFailures->removeTestCaseInstanceFailure($testCaseInstanceName);
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
		KalturaTestListener::$failureFilePath = $failureFilePath;
		KalturaTestListener::$failuresFile = fopen($failureFilePath, 'w+');
		
	}
	
	/**
	 * 
	 * Sets the listeners data file path
	 * @param string $dataFilePath
	 */
	public static function setDataFilePath($dataFilePath)
	{
		KalturaTestListener::$dataFilePath = $dataFilePath;
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
				
				KalturaTestResultUpdater::UpdateResults(KalturaTestListener::$dataFilePath,KalturaTestListener::$failureFilePath);
			}
			else
			{
				print("failures XML is null!!!\n");
				KalturaLog::debug("failures XML is null!!!");
				var_dump($testCaseFailuresXml);
			}
		}
	}
}