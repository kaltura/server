<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents a unit test failure (single failure on a field)
 * @author Roni
 *
 */
class KalturaFailure
{
	/**
	 * 
	 * Creates a new unit test failure
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
	 * The unit test object field which has the error
	 * @var unknown_type
	 */
	public $field;
	
	/**
	 * 
	 * The unit test assert that was performed
	 * @var unknown_type
	 */
	public $assert;
	
	/**
	 * 
	 * The field actual value
	 * @var unknown_type
	 */
	public $actualValue;
	
	/**
	 * 
	 * The Error message
	 * @var unknown_type
	 */
	public $message;
	
	/**
	 * 
	 * The field output reference value
	 * @var unknown_type
	 */
	public $outputReferenceValue;
	
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
}
