<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * All the tests failures
 * @author Roni
 *
 */
class KalturaTestCaseFailures
{
	/**
	 * 
	 * Creates a new test failures (for entire run of tests)
	 */
	function __construct($testCaseName = '')
	{
		$this->testProceduresFailures = array();
		$this->testCaseName = $testCaseName;
	}
	
	/**
	 * The test name
	 */
	private $testCaseName = null;
	 
	/**
	 * 
	 * Holds all the test procedures failures
	 * @var array<KalturaTestProcedureFailure>
	 */
	private $testProceduresFailures = array();
	
	/**
	 * 
	 * Adds a new KalturaTestProcedureFailure to the testCaseFailures
	 */
	public function addTestProcedureFailure(KalturaTestProcedureFailure $testProcedureFailure)
	{
		if($this->testProceduresFailures == null)
		{
			$this->testProceduresFailures = array();
		}
		
		array_push($this->testProceduresFailures, $testProcedureFailure);
	}
	
	/**
	 * @return the $testName
	 */
	public function getTestCaseName() {
		return $this->testCaseName;
	}

	/**
	 * @return the $testCaseFailures
	 */
	public function getTestProceduresFailures() {
		return $this->testProceduresFailures;
	}

	/**
	 * @param field_type $testName
	 */
	public function setTestCaseName($testCaseName) {
		$this->testCaseName = $testCaseName;
	}

	/**
	 * @param array<KalturaTestProcedureFailure> $testCaseFailures
	 */
	public function setTestProceduresFailures($testProcedureFailures) {
		$this->testProceduresFailures = $testProcedureFailures;
	}

	/**
	 * 
	 * Removes a test procedure failures with the given key
	 * @param string $testProcedureKey
	 */
	public function removeTestProcedureFailure($testProcedureKey)
	{
		unset($this->testProceduresFailures[$testProcedureKey]);
	}
	
	/**
	 * 
	 * Returns the test cases failures as a DomDocument
	 * @throws Exception
	 * @return DomDocument
	 */
	public static function toXML(KalturaTestCaseFailures $testCaseFailures, $rootNodeName='data')
	{
		if(count($testCaseFailures->getTestProceduresFailures()) == 0)
		{
			return "";
		}
		
		$dom = new DOMDocument("1.0");
		
		//Create elements in the Dom referencing the entire test data file
		$testCaseElement = $dom->createElement($rootNodeName);
		$testCaseElement->setAttribute("testCaseName", $testCaseFailures->getTestCaseName());
		$dom->appendChild($testCaseElement);
	
		//For each test data
		foreach ($testCaseFailures->getTestProceduresFailures() as $testProcedureFailure)
		{
			//Create the xml from the object
			$objectAsDOM = KalturaTestProcedureFailure::toXml($testProcedureFailure, "TestProcedureFailures");
		 
			kXml::appendDomToElement($objectAsDOM, &$testCaseElement, $dom);
		}
		
		return $dom;
	}
	
	/**
	 * 
	 * Generates a new KalturaTestsFailures object from a given failure file path 
	 * @param string $failureFilePath
	 */
	public static function generateFromXml($failureFilePath)
	{
		$testsFailures = new KalturaTestCaseFailures();
		$testsFailures->fromXml($failureFilePath);
		return $testsFailures;		
	}
	
	/**	
	 * 
	 * Generates a new KalturaTestsFailures object from a given failure file path 
	 * @param string $failureFilePath
	 */
	public function fromXml($failureFilePath)
	{
		$simpleXML = kXml::openXmlFile($failureFilePath);

		$this->testCaseName = $simpleXML["TestCaseName"]; 
		
		foreach ($simpleXML->TestProcedureFailures as $testProcedureFailureXml)
		{
			$testProcedureFailure = KalturaTestProcedureFailure::generateFromXml($testProcedureFailureXml);
			$this->testProceduresFailures[$testProcedureFailure->getTestProcedureName()] = $testProcedureFailure; 
						
		}
	}
}