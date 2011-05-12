<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents a test failure (single failure on a field)
 * @author Roni
 *
 */
class KalturaFailure
{
	/**
	 * 
	 * Creates a new test failure
	 * @param string $field
	 * @param unknown_type $actualValue
	 * @param unknown_type $outputReferenceValue
	 */
	function __construct($field, $actualValue, $outputReferenceValue, $assert = null, $message = null)
	{
		$this->field = $field;
		$this->outputReferenceValue = $outputReferenceValue;
		$this->actualValue = $actualValue;
		$this->assert = $assert;
		$this->message = $message;
	}
	
	/**
	 * 
	 * The test object field which has the error
	 * @var unknown_type
	 */
	private $field;
	
	/**
	 * 
	 * The test assert that was performed
	 * @var unknown_type
	 */
	private $assert;
	
	/**
	 * 
	 * The field actual value
	 * @var unknown_type
	 */
	private $actualValue;
	
	/**
	 * 
	 * The Error message
	 * @var unknown_type
	 */
	private $message;
	
	/**
	 * 
	 * The field output reference value
	 * @var unknown_type
	 */
	private $outputReferenceValue;
	
	/**
	 * @return the $field
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * @return the $assert
	 */
	public function getAssert() {
		return $this->assert;
	}

	/**
	 * @return the $actualValue
	 */
	public function getActualValue() {
		return $this->actualValue;
	}

	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return the $outputReferenceValue
	 */
	public function getOutputReferenceValue() {
		return $this->outputReferenceValue;
	}

	/**
	 * @param unknown_type $field
	 */
	public function setField($field) {
		$this->field = $field;
	}

	/**
	 * @param unknown_type $assert
	 */
	public function setAssert($assert) {
		$this->assert = $assert;
	}

	/**
	 * @param unknown_type $actualValue
	 */
	public function setActualValue($actualValue) {
		$this->actualValue = $actualValue;
	}

	/**
	 * @param unknown_type $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * @param unknown_type $outputReferenceValue
	 */
	public function setOutputReferenceValue($outputReferenceValue) {
		$this->outputReferenceValue = $outputReferenceValue;
	}

	/**
	 * 
	 * Returns a well formatted string witht 
	 */
	public function __toString()
	{
		return ("Failure: on field " . $this->field . "\n" .
				"Output Reference value: " . $this->outputReferenceValue . "\n" .
				"Actual output value:  : " . $this->actualValue . "\n" .
				"Message: {$this->message}\n\n");
	}

	/**
	 * 
	 * Generates a new KalturaFailure from a given SimpleXMLElement
	 * @param SimpleXMLElement $failureXml
	 */
	public static function generateFromXml(SimpleXMLElement $failureXml)
	{
		$field = (string)$failureXml->Field;

		$assert = null;
		$message = null;
		
		if(isset($failureXml->Assert))
		{
			$assert = $failureXml->Assert;
		}
		
		if(isset($failureXml->Message))
		{
			$message = $failureXml->Message;
		}
		
		//If we have an array
		if(isset($failureXml->ActualOutput->Array))
		{
			$actualValue = KalturaFailure::getArrayValueFromXML($failureXml->ActualOutput->Array);
		}
		else
		{
			//Convert the simleXMl into string
			$actualValue = (string)$failureXml->ActualOutput;

			//No string was given
			if(strlen($actualValue) == 0)
			{
				$actualValue = null;
			}
		}
		
		if(isset($failureXml->OutputReference->Array))
		{
			$outputReferenceValue = KalturaFailure::getArrayValueFromXML($failureXml->OutputReference->Array);
		}
		else
		{
			$outputReferenceValue = (string)$failureXml->OutputReference;
			
			//No string was given
			if(strlen($outputReferenceValue) == 0)
			{
				$outputReferenceValue = null;
			}
		} 
		
		$kalturaFailure = new KalturaFailure($field, $actualValue, $outputReferenceValue, $assert, $message);
		return $kalturaFailure;
	}
	
	/**
	 * 
	 * Returns a xml array as a new object for the KalturaTestFailure
	 * @param unknown_type $arrayAsXml
	 */
	private static function getArrayValueFromXML($arrayAsXml)
	{
		$arrayValue = array();
		
		foreach ($arrayAsXml->children() as $singleElementKey => $singleElementValue)
		{
			$key = (string)$singleElementValue["key"];
			$arrayValue[$key] = (string)$singleElementValue;
		}
		
		return $arrayValue;
	}

	/**
	 * 
	 * Returns a DomDocument containing the kaltura failure
	 * @param KalturaFailure $kalturaFailure
	 * @param unknown_type $rootNodeName
	 * @return DomDocument
	 */
	public static function toXml(KalturaFailure $kalturaFailure, $rootNodeName='data')
	{
		$dom = new DOMDocument("1.0"); 
		
		$failureNode = $dom->createElement($rootNodeName);
		$dom->appendChild($failureNode);	
		
//		if(!is_null($kalturaFailure->getField()))
		{
			$fieldNode = $dom->createElement("Field", $kalturaFailure->getField());
			$failureNode->appendChild($fieldNode);	
		}
				
//		if (!is_null($kalturaFailure->getOutputReferenceValue()))
		{
			$outputReferenceNode = $dom->createElement("OutputReference");
			$failureNode->appendChild($outputReferenceNode);
			KalturaFailure::setElementValue($dom, $outputReferenceNode, $kalturaFailure->getOutputReferenceValue(), $kalturaFailure->getField());
		}
		
//		if (!is_null($kalturaFailure->getActualValue()))
		{
			$actualOutputNode = $dom->createElement("ActualOutput");
			$failureNode->appendChild($actualOutputNode);
			KalturaFailure::setElementValue($dom, $actualOutputNode, $kalturaFailure->getActualValue(), $kalturaFailure->getField());
		}
					
//		if (!is_null($kalturaFailure->getAssert()))
		{
			$assertNode = $dom->createElement("Assert");
			$failureNode->appendChild($assertNode);
			KalturaFailure::setElementValue($dom, $assertNode, $kalturaFailure->getAssert());
		}
		
		if (!is_null($kalturaFailure->getMessage()))
		{
			$messageNode = $dom->createElement("Message");
			$failureNode->appendChild($messageNode );
			KalturaFailure::setElementValue($dom, $messageNode , $kalturaFailure->getMessage());
		}
		
		return $dom;
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
