<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents a single test data
 * @author Roni
 *
 */
class KalturaTestCaseInstanceData
{
	/**
	 * The test case instance name (Defaulted to "")
	 * @var string
	 */
	private $testCaseInstanceName = "";
	 
	/**
	 * 
	 * The Test input
	 * @var array<KalturaTestDataBase>
	 */
	private $input = array();
	
	/**
	 * 
	 * The test output reference
	 * @var array<KalturaTestDataObject>
	 */
	private $outputReference = array();

	/**
	 * 
	 * The test case data configuration (used mainly for conifgurationd for API)  
	 * @var unknown_type
	 */
	private $testCaseDataConfiguration = null;   
		
	/**
	 * @return the $testCaseInstanceName
	 */
	public function getTestCaseInstanceName() {
		return $this->testCaseInstanceName;
	}

	/**
	 * @param string $testCaseInstanceName
	 */
	public function setTestCaseInstanceName($testCaseInstanceName) {
		$this->testCaseInstanceName = $testCaseInstanceName;
	}

	/**
	 * 
	 * Creates a new test case instance
	 * @param string $testCaseInstanceName
	 * @param array $testCaseInstanceInput
	 * @param array $testCaseInstanceOutputReference
	 * @param unknown_type $testCaseInstanceConfiguration
	 */
	public function __construct($testCaseInstanceName = "", array $testCaseInstanceInput = array(), array $testCaseInstanceOutputReference = array(), $testCaseInstanceConfiguration = null)
	{
		$this->testCaseInstanceName = $testCaseInstanceName;
		$this->input = $testCaseInstanceInput;
		$this->outputReference = $testCaseInstanceOutputReference;
		$this->testCaseDataConfiguration = $testCaseInstanceConfiguration;
	}
	 
	/**
	 * @return the $input
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * @return the $outputReference
	 */
	public function getOutputReference() {
		return $this->outputReference;
	}

	/**
	 * @return the $testCaseDataConfiguration
	 */
	public function getTestCaseDataConfiguration() {
		return $this->testCaseDataConfiguration;
	}

	/**
	 * @param array<KalturaTestDataBase> $input
	 */
	public function setInput(array $input) {
		$this->input = $input;
	}

	/**
	 * @param array<KalturaTestDataBase> $outputReference
	 */
	public function setOutputReference(array $outputReference) {
		$this->outputReference = $outputReference;
	}

	/**
	 * 
	 * Add a new input to the test case instance data
	 * @param KalturaTestDataBase $input
	 */
	public function addInput(KalturaTestDataBase $input)
	{
		if($this->input == null)
		{
			$this->input = array();
		}
		
		$this->input[] = $input;
	}
	
	/**
	 * 
	 * Add a new output reference to the test case instance data
	 * @param KalturaTestDataBase $outputReference
	 */
	public function addOutputReference(KalturaTestDataBase $outputReference)
	{
		if($this->outputReference== null)
		{
			$this->outputReference= array();
		}
		
		$this->outputReference[] = $outputReference;
	}
	
	/**
	 * @param unknown_type $testCaseDataConfiguration
	 */
	public function setTestCaseDataConfiguration($testCaseDataConfiguration) {
		$this->testCaseDataConfiguration = $testCaseDataConfiguration;
	}

	/**
	 * 
	 * Generates a new TestCaseInstanceData object from a given simpleXLMElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * @return KalturaTestCaseInstanceData - New test case instance data object
	 */
	public static function generateFromDataXml(SimpleXMLElement $testCaseInsatanceXml)
	{
		$testCaseInstanceData = new KalturaTestCaseInstanceData();
		$testCaseInstanceData->fromDataXml($testCaseInsatanceXml);
		return $testCaseInstanceData;
	}
	
	/**
	 * 
	 * Generates a new TestCaseInstanceData object from a given simpleXLMElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * @return TestCaseInstanceData - New test case instance data object
	 */
	public function fromDataXml(SimpleXMLElement $testCaseInstanceXml)
	{
		if(isset($testCaseInstanceXml["testCaseInstanceName"]))
		{
			$this->testCaseInstanceName = ((string)$testCaseInstanceXml["testCaseInstanceName"]);
		}
		
		foreach ($testCaseInstanceXml->Input as $input)
		{
			$testDataObject = KalturaTestDataObject::generatefromXml($input);
			$this->input[] = $testDataObject;
		}
		
		foreach ($testCaseInstanceXml->OutputReference as $outputReference)
		{
			$testDataObject = KalturaTestDataObject::generatefromXml($outputReference);
			$this->outputReference[] = $testDataObject;
		}
	}

	/**
	 * 
	 * Returns the given KalturaTestCaseInstanceData as a DomDocument
	 * @param KalturaTestCaseInstanceData $testCaseInstanceData
	 */
	public static function toXml(KalturaTestCaseInstanceData $testCaseInstanceData)
	{
		$dom = new DOMDocument("1.0");
		
		//Create all his elements
		$domTestCaseData = $dom->createElement("TestCaseData");
		$domTestCaseData->setAttribute("testCaseInstanceName", $testCaseInstanceData->getTestCaseInstanceName());
		$dom->appendChild($domTestCaseData);
					
		//For each input:
		foreach ($testCaseInstanceData->getInput() as $input)
		{
			//Create the xml from the object
			$objectAsDOM = KalturaTestDataObject::toXml($input, "Input");
	 		kXml::appendDomToElement($objectAsDOM, &$domTestCaseData, $dom);
		}
		
		//For each outputReference:
		foreach ($testCaseInstanceData->getOutputReference() as $outputReference)
		{
			//Create the xml from the object
			$objectAsDOM = KalturaTestDataObject::toXml($outputReference, "OutputReference");
	 		kXml::appendDomToElement($objectAsDOM, &$domTestCaseData, $dom);
		}
		
		return $dom;
	}
}