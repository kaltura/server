<?php
/**
 * 
 * Represents a test procedure failure
 * @author Roni
 *
 */
class KalturaTestProcedureFailure
{
	/**
	 * 
	 * Creates a new kaltura test procedure failure object
	 * @param string $testProcedureName
	 */
	public function __construct($testProcedureName = null)
	{
		$this->testProcedureName = $testProcedureName;
		$this->testCaseInstanceFailures = array();
	}

	/**
	 * 
	 * The test procedure name
	 * @var string
	 */
	private $testProcedureName = null;

	/**
	 * 
	 * The test procedure test case instance failures
	 * @var KalturaTestCaseInstanceFailure
	 */
	private $testCaseInstanceFailures = array();
	
	/**
	 * 
	 * Adds a new test case instance failure.
	 * @param KalturaTestCaseInstanceFailure $testCaseInstanceFailure
	 */
	public function addTestCaseInstanceFailure(KalturaTestCaseInstanceFailure $testCaseInstanceFailure)
	{
		if($this->testCaseInstanceFailures == null)
		{
			$this->testCaseInstanceFailures = array();
		}
		
		array_push($this->testCaseInstanceFailures, $testCaseInstanceFailure); 
	}

	/**
	 * 
	 * Removes the test case instance failure with the given key
	 * @param string $testCaseInstanceFailurekey
	 */
	public function removeTestCaseInstanceFailure($testCaseInstanceFailurekey)
	{
		unset($this->testCaseInstanceFailures[$testCaseInstanceFailurekey]);
	}
	
	/**
	 * @return the $testProcedureName
	 */
	public function getTestProcedureName() {
		return $this->testProcedureName;
	}

	/**
	 * @return the $testCaseInstanceFailures
	 */
	public function getTestCaseInstanceFailures() {
		return $this->testCaseInstanceFailures;
	}
	
	/**
	 * @param string $testProcedureName
	 */
	public function setTestProcedureName($testProcedureName) {
		$this->testProcedureName = $testProcedureName;
	}

	/**
	 * @param KalturaTestCaseInstanceFailures $testCaseInstanceFailures
	 */
	public function setTestCaseInstanceFailures(KalturaTestCaseInstanceFailures $testCaseInstanceFailures) {
		$this->testCaseInstanceFailures = $testCaseInstanceFailures;
	}

	/**
	 * 
	 * Returns all the failures as XML formatted string
	 * @throws Exception
	 */
	public static function toXML(KalturaTestProcedureFailure $testProcedureFailure, $rootNodeName='data')
	{
		if(count($testProcedureFailure->getTestCaseInstanceFailures()) == 0)
		{
			return "";
		}

		$dom = new DOMDocument("1.0");
		
		//Create elements in the Dom referencing the entire test data file
		$testProcedureElement = $dom->createElement($rootNodeName);
		$testProcedureElement->setAttribute("testProcedureName", $testProcedureFailure->getTestProcedureName());
		$dom->appendChild($testProcedureElement);

		//For each unit test data
		foreach ($testProcedureFailure->getTestCaseInstanceFailures() as $testCaseInstanceFailure)
		{
			//Create the xml from the object
			$objectAsDOM = KalturaTestCaseInstanceFailure::toXml($testCaseInstanceFailure, "TestCaseInstance");
		 	kXml::appendDomToElement($objectAsDOM, &$testProcedureElement, $dom);
		}

		return $dom;
	}
	
	/**
	 * 
	 * Generates a new KalturaTestsFailures object from a given failure file path 
	 * @param string $failureFilePath
	 */
	public static function generateFromXml($xmlTestProcedure)
	{
		$testsProcedureFailures = new KalturaTestProcedureFailure();
		$testsProcedureFailures->fromXml($xmlTestProcedure);
		return $testsProcedureFailures;		
	}
	
	/**	
	 * 
	 * Generates a new KalturaTestProcedureFailure object from a given SimpleXmlElement
	 * @param string $failureFilePath
	 */
	public function fromXml(SimpleXmlElement $xmlTestProcedure)
	{
		$this->testProcedureName = (string)$xmlTestProcedure["testProcedureName"];
		
		foreach ($xmlTestProcedure->TestCaseInstance as $unitTestFailureXml)
		{
			$testCaseInstanceFailure = KalturaTestCaseInstanceFailure::generateFromXml($unitTestFailureXml);
			$this->testCaseInstanceFailures[$testCaseInstanceFailure->getTestCaseInstanceName()] = $testCaseInstanceFailure; 
						
		}
	}
}