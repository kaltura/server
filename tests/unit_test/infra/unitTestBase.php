<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

//TODO: What will be our interface for the unit tests?
interface IUnitTest
{
	
}

/**
 * 
 * The Base class for all our unit tests, can be run in PHPUnit
 * @author Roni
 *
 */
class UnitTestBase extends PHPUnit_Framework_TestCase
{

	/**
	 * 
	 * Indicates wheter the test has failures
	 * @var bool
	 */
	public $hasFailures = false;

	/**
	 * 
	 * Returns the inputs for the test
	 */
	public function getInputs()
	{
		return $this->data;
	}
	
//	/**
//	 * 
//	 * Ctor for all the unit tests designed to init all the unit test infrastructure
//	 */
//	function __construct($name = "", $data = null)
//	{
//		if(UnitTestBase::$failureFile == null)
//		{
//			UnitTestBase::$failureFile = fopen(dirname(__FILE__) . "/../unitTests/Results/KDLUnitTest.result", "w+");
//			UnitTestBase::$failureObjectsFile = fopen(dirname(__FILE__) . "/../unitTests/Results/KDLUnitTest.failures", "w+");
//			UnitTestBase::$failures = new testsErrors();
//			UnitTestBase::$listener = new kalturaUnitTestListener();
//		}
//		
//		if($this->result != null)
//		{
//			$this->result->addListener(UnitTestBase::$listener);
//		}
//		
//		parent::__construct();
//							
//		//TODO: add the tests listener (so we can get a custom failure output)
//	}
			
	
	/**
	 * 
	 * The unit tests results
	 * @var unknown_type
	 */
	public static $results;
	
	public function runTest()
	{
		//Do this section only once per test file and not for test... so we can initiate all the tests 
		//TODO: HOW to do nice :) and also how to know if this is a new test class or a new test or jsut another input
		$this->currentFailure = null;
		
		if(UnitTestBase::$failureFile == null)
		{
			$class = get_class($this);

			$classPath = KAutoloader::getClassFilePath($class);
			
			UnitTestBase::$failureFile = fopen(dirname($classPath) . "/testsData/KDLUnitTest.result", "w+");
			UnitTestBase::$failureObjectsFile = fopen(dirname($classPath) . "/testsData/KDLUnitTest.failures", "w+");
			$this->result->addListener(new kalturaUnitTestListener());
		}
		
		parent::runTest();
	}
	
	/**
	 * 
	 * The unit tests listener
	 * @var kalturaUnitTestListener
	 */
	public static $listener = null;
	
	/**
	 * 
	 * The unit test failure file for custom failures (humna readable interface)
	 * @var unknown_type
	 */
	public static $failureFile = null; 
	
	/**
	 * 
	 * The unit test failures 
	 * @var testFailures
	 */
	public static $failures = null;
	
	/**
	 * 
	 * The unit test current failure 
	 * @var the current failure (used by the listener)
	 */
	public $currentFailure = null;
	
	/**
	 * 
	 * All the failures object will be parsed here so we can use them later for failure reporting and unittest overriding
	 * @var unknown_type
	 */
	public static $failureObjectsFile = null;
	
	/**
	 * 
	 * The unit test data provider (gets the data for the different unit tests)
	 * @param string $dataFilePath - the data file path (with the objects)
	 * @return array<array>();
	 */
	public static function provider($dataFilePath)
	{
		$simpleXML = kXml::openXmlFile($dataFilePath);
				
		$inputsForUnitTests = array();
		
		foreach ($simpleXML->UnitTestsData->UnitTestData as $unitTestData)
		{
			$inputs = array();
			
			foreach ($unitTestData->Inputs->Input as $input)
			{
				$object = UnitTestDataObject::fromXml($input);

				//Go to the last and current input and add the variable
				array_push($inputs, $object);
			}
			
			foreach ($unitTestData->OutputReferences->OutputReference as $output)
			{
				$object = UnitTestDataObject::fromXml($output);

				//Go to the last and current input and add the variable
				array_push($inputs, $object);
			}
			
			$inputsForUnitTests[] = $inputs;
		}
		
		return $inputsForUnitTests; 
	}

	/**
	 * 
	 * Compares two propel objects using the first objects feilds (using getByNameMethod)
	 * @param BaseObject $outputReference
	 * @param BaseObject $newResult
	 * @return bool, if the objects are equal
	 */
	public function comparePropelObjectsByFields($outputReference, $newResult, $validErrorFields)
	{
		//Gets the data peer of the object (used to geting all the obejct feilds)
		$dataPeer = $outputReference->getPeer(); 
		
		$outputReferenceId = $outputReference->getId();
		$newResultId = $newResult->getId();
		
		//Gets all object feilds
		$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
		
		$isEqual = true;
		
		$newErrors = array();
		
		//Create the xml elements by all fields and their values
		foreach ($fields as $field)
		{
			PHPUnit_Util_Timer::start();
			
			//If the field is in the valid failure list then we skip him 
			if(in_array($field, $validErrorFields))
			{
				continue;
			}
			else 
			{
				$expectedValue = $outputReference->getByName($field);
				$actualValue = $newResult->getByName($field);
				
				//if this is an array we need to change it to a string
				
				try {
					$currentFailure = new unitTestFailure($field, $actualValue, $expectedValue);
					$this->assertEquals($expectedValue, $actualValue, $currentFailure);
				}
				catch (PHPUnit_Framework_AssertionFailedError $e) {
					$this->hasFailures  = true;
					$this->currentFailure = $currentFailure;
					$this->result->addFailure($this, $e, PHPUnit_Util_Timer::stop());
				}
				catch (Exception $e) {
					$this->result->addError($this, $e, PHPUnit_Util_Timer::stop());
				}
			}
		}

		return $newErrors;
	}
}
