<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents a test case instance failure (on a single input)
 * @author Roni
 *
 */
class KalturaTestCaseInstanceFailure
{
	/**
	 * 
	 * Creates a new test case failure for the given input and their failures
	 * @param array<> $inputs
	 * @param array<> $failures
	 */
	function __construct($testCaseInstanceName ='No Name Given', array $inputs = array() , array $failures = array())
	{
		$this->testCaseInput = $inputs;
		$this->failures = $failures;
		$this->testCaseInstanceName = $testCaseInstanceName;
	}
	
	/**
	 * 
	 * The inputs for the test case instance
	 * @var array<KalturaTestDataBase>
	 */
	private $testCaseInput = null;
	
	/**
	 * 
	 * The test case instance name
	 * @var string
	 */
	private $testCaseInstanceName = null;
	
	/**
	 * 
	 * The test case instance failures
	 * @var array<KalturaFailure>
	 */
	private $failures = null;
	
	/**
	 * @return the $testCaseInput
	 */
	public function getTestCaseInput() {
		return $this->testCaseInput;
	}

	/**
	 * @return the $testCaseInstanceName
	 */
	public function getTestCaseInstanceName() {
		return $this->testCaseInstanceName;
	}

	/**
	 * @return the $failures
	 */
	public function getFailures() {
		return $this->failures;
	}

	/**
	 * @param array<unknown_type> $testCaseInput
	 */
	public function setTestCaseInput($testCaseInput) {
		$this->testCaseInput = $testCaseInput;
	}

	/**
	 * @param string $testCaseInstanceName
	 */
	public function setTestCaseInstanceName($testCaseName) {
		$this->testCaseInstanceName = $testCaseName;
	}

	/**
	 * @param array<KalturaFailure> $testCaseFailures
	 */
	public function setFailures($testCaseFailures) {
		$this->failures = $testCaseFailures;
	}

	/**
	 * 
	 * Returns a well formated string output representing the kaltura test case instance failure
	 */
	public function __toString()
	{
		$string = "";
		
		$string = "inputs = ";
		foreach ($this->testCaseInput as $inputKey => $inputValue)
		{
			$string = $string . $inputKey . $inputValue;
		}
		
		$string = "\n";
		
		foreach ($this->testCaseFailures as $failure)
		{
			$string .= $failure;
		}
		return $string; 
	}

	/**
	 * 
	 * Adds a new failure to the test case instance failures
	 * @param KalturaFailure $failure
	 */
	public function addFailure(KalturaFailure $failure)
	{
		if($this->failures == null)
		{
			$this->failures = array();
		}
		
		array_push($this->failures, $failure);
	}
	
	/**
	 * 
	 * Generates a new testCaseFailure object from a given simpleXmlElement (failure file xml)
	 * @param SimpleXMlElement $unitTestFailureXml
	 */
	public static function generateFromXml(SimpleXMlElement $testCaseInstanceFailureXml)
	{
		$testCaseFailure = new KalturaTestCaseInstanceFailure();
		$testCaseFailure->fromXml($testCaseInstanceFailureXml);
		return $testCaseFailure;
	}
	
	/**
	 * 
	 * Generates a new testCaseFailure object from a given simpleXmlElement (failure file xml)
	 * @param SimpleXMlElement $unitTestFailureXml
	 */
	public function fromXml(SimpleXMlElement $testCaseInstanceFailureXml)
	{
		$this->testCaseInstanceName = (string)$testCaseInstanceFailureXml["testCaseInstanceName"];
		
		//Sets the inputs as key => value byt the xml attributes
		foreach ($testCaseInstanceFailureXml->Inputs->Input as $inputXml)
		{
			$this->testCaseInput[] = kXml::getAttributesAsArray($inputXml);
		}
		
		foreach ($testCaseInstanceFailureXml->Failures->Failure as $failureXml)
		{
			$this->failures[] = KalturaFailure::generateFromXml($failureXml);
		}
	}
	
	/**
	 * 
	 * Returns a DomDocument containing the test case failure
	 * @param KalturaTestCaseInstanceFailure $failure
	 * @param string $rootNodeName - default to 'data'
	 * @return DOMDocument - the xml for the given error
	 */
	public static function toXml(KalturaTestCaseInstanceFailure $testCaseInstanceFailure, $rootNodeName = 'data')
	{
		if(count($testCaseInstanceFailure->getFailures()) == 0)
		{
			return new DOMDocument("1.0");
		}
		
		$dom = new DOMDocument(1.0);
		$rootNode = $dom->createElement($rootNodeName);
		$dom->appendChild($rootNode);
		$rootNode->setAttribute("testCaseInstanceName", $testCaseInstanceFailure->getTestCaseInstanceName());
				
		$inputsNode = $dom->createElement("Inputs");
				
		foreach ($testCaseInstanceFailure->getTestCaseInput() as $inputKey => $inputValue)
		{
			$node = $dom->createElement("Input");
			
			$type = gettype($inputValue);
			
			if(class_exists(get_class($inputValue)))
			{
				$type = get_class($inputValue);
			}
			 
			$node->setAttribute("type", $type);
			
			$id = $inputValue;
			
			//TODO: add support for non propel objects
			if($inputValue instanceof BaseObject)
			{
				$id = $inputValue->getId();
			}
			
			$node->setAttribute($type."Id", $id);
			$inputsNode->appendChild($node);
		}
		
		$failuresNode = $dom->createElement("Failures");
		
		foreach ($testCaseInstanceFailure->getFailures() as $kalturaFailure)
		{
			$objectAsDOM = KalturaFailure::toXml($kalturaFailure, "Failure");
			
			kXml::appendDomToElement($objectAsDOM, &$failuresNode, $dom);
		}
											
		$rootNode->appendChild($inputsNode);
		$rootNode->appendChild($failuresNode);
	
		//pass back DomElement object
		return $dom;
	}
}