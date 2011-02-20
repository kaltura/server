<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents a test case failure (on a single input)
 * @author Roni
 *
 */
class KalturaTestCaseFailure
{
	/**
	 * 
	 * Creates a new test case failure for the given input and their failures
	 * @param array<> $inputs
	 * @param array<> $failures
	 */
	function __construct(array $inputs = array(), array $failures = array())
	{
		$this->testCaseInput = $inputs;
		$this->testCaseFailures  = $failures;
	}
	
	/**
	 * 
	 * The inputs for the unit test
	 * @var array<unknown_type>
	 */
	public $testCaseInput = null;
	
	/**
	 * 
	 * The unit test failures
	 * @var array<KalturaFailure>
	 */
	public $testCaseFailures = null;
	
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
	 * Generates a new testCaseFailure object from a given simpleXmlElement (failure file xml)
	 * @param SimpleXMlElement $unitTestFailureXml
	 */
	public static function generateFromXml(SimpleXMlElement $unitTestFailureXml)
	{
		$testCaseFailure = new KalturaTestCaseFailure();
		$testCaseFailure->fromXml($unitTestFailureXml);
		return $testCaseFailure;
	}
	
	/**
	 * 
	 * Generates a new testCaseFailure object from a given simpleXmlElement (failure file xml)
	 * @param SimpleXMlElement $unitTestFailureXml
	 */
	public function fromXml(SimpleXMlElement $unitTestFailureXml)
	{
		//Sets the inputs as key => value byt the xml attributes
		foreach ($unitTestFailureXml->Inputs->Input as $inputXml)
		{
			$this->testCaseInput[] = kXml::getAttributesAsArray($inputXml);
		}
		
		foreach ($unitTestFailureXml->Failures->Failure as $failureXml)
		{
			$this->testCaseFailures[] = KalturaFailure::generateFromXml($failureXml);
		}
	}
	
	/**
	 * 
	 * Sets the given object's given property value 
	 * @param testCaseFailure $failure
	 * @param string $rootNodeName - default to 'data'
	 * @return DOMDocument - the xml for the given error
	 */
	public static function toXml(KalturaTestCaseFailure $failure, $rootNodeName = 'data')
	{
		if(count($failure->testCaseFailures) == 0)
		{
			return "";
		}
		
		$xml = new DOMDocument(1.0);
		$rootNode = $xml->createElement($rootNodeName);
		$xml->appendChild($rootNode);
		
		$inputsNode = $xml->createElement("Inputs");
				
		foreach ($failure->testCaseInput as $inputKey => $inputValue)
		{
			//TODO: add support for non propel objects
			$node = $xml->createElement("Input");
			
			$type = gettype($inputValue);
			
			if(class_exists(get_class($inputValue)))
			{
				$type = get_class($inputValue);
			}
			 
			$node->setAttribute("type", $type);
			
			$id = $inputValue;
			
			if($inputValue instanceof BaseObject)
			{
				$id = $inputValue->getId();
			}
			
			$node->setAttribute($type."Id", $id);
			$inputsNode->appendChild($node);
		}
		
		$failuresNode = $xml->createElement("Failures");
		
		foreach ($failure->testCaseFailures as $kalturaFailure)
		{
			$failureNode = $xml->createElement("Failure");
			
			$fieldNode = $xml->createElement("Field", $kalturaFailure->field);
			$failureNode->appendChild($fieldNode);

			$outputReferenceNode = $xml->createElement("OutputReference");
			$failureNode->appendChild($outputReferenceNode);
			KalturaTestCaseFailure::setElementValue($xml, $outputReferenceNode, $kalturaFailure->outputReferenceValue, $kalturaFailure->field);

			$actualOutputNode = $xml->createElement("ActualOutput");
			$failureNode->appendChild($actualOutputNode);
			KalturaTestCaseFailure::setElementValue($xml, $actualOutputNode, $kalturaFailure->actualValue, $kalturaFailure->field);
			
			if ($kalturaFailure->assert != null)
			{
				$assertNode = $xml->createElement("Assert");
				$failureNode->appendChild($assertNode);
				KalturaTestCaseFailure::setElementValue($xml, $assertNode, $kalturaFailure->assert);
			}
			
			if ($kalturaFailure->message != null)
			{
				$messageNode = $xml->createElement("Message");
				$failureNode->appendChild($messageNode );
				KalturaTestCaseFailure::setElementValue($xml, $messageNode , $kalturaFailure->message);
			}
			
			$failuresNode->appendChild($failureNode);
		}
											
		$rootNode->appendChild($inputsNode);
		$rootNode->appendChild($failuresNode);
	
		//pass back DomElement object
		return $xml;
	}
	
	/**
	 * 
	 * Sets the given node it's value (if is array and if not)
	 * @param DomDocumnet $xml
	 * @param SimpleXmlElement $rootNode
	 * @param unknown_type $value
	 * @param string $fieldName
	 * @param string $fieldType
	 */
	private static function setElementValue(DOMDocument &$xml, DomElement &$rootNode, $value, $fieldName = null, $fieldType = null)
	{
		//If the value is not an array then we just create the element and sets it's value
		if(!is_array($value ))
		{
			$rootNode->nodeValue = $value;
			if($fieldType != null)
			{
				$rootNode->setAttribute("type", $fieldType);
			}
		}
		else
		{
			//create the array node
			$arrayNode = $xml->createElement("Array");
			
			foreach ($value as $key => $singleValue)
			{
				$node = $xml->createElement($fieldName, $singleValue);
						
				$node->setAttribute("key", $key );
				
				if($fieldType != null)
				{
					$$node->setAttribute("type", $fieldType);
				}
				
				$arrayNode->appendChild($node);
			}
								
			$rootNode->appendChild($arrayNode);
		}
	}
}