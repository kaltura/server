<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

/**
 * 
 * Represents a unit test failures (single failure on a field)
 * @author Roni
 *
 */
class unitTestFailure
{
	/**
	 * 
	 * Creates a new unit test failure
	 * @param string $field
	 * @param unknown_type $actualValue
	 * @param unknown_type $outputReferenceValue
	 */
	function __construct($field, $actualValue, $outputReferenceValue)
	{
		$this->field = $field;
		$this->outputReferenceValue = $outputReferenceValue;
		$this->actualValue = $actualValue;
	}
	
	/**
	 * 
	 * The unit test object field which has the error
	 * @var unknown_type
	 */
	public $field;
	
	/**
	 * 
	 * The field actual value
	 * @var unknown_type
	 */
	public $actualValue;
	
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
				"Actual output value:  : " . $this->actualValue . "\n\n");
	}

	/**
	 * 
	 * Generates a new unitTestFailure from a given SimpleXMLElement
	 * @param SimpleXMLElement $failureXml
	 */
	public static function generateFromXml(SimpleXMLElement $failureXml)
	{
		$field = (string)$failureXml->Field;

		//If we have an array
		if(isset($failureXml->ActualOutput->Array))
		{
			$actualValue = unitTestFailure::getArrayValueFromXML($failureXml->ActualOutput->Array);
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
			$outputReferenceValue = unitTestFailure::getArrayValueFromXML($failureXml->OutputReference->Array);
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
		
		$unitTestFailure = new unitTestFailure($field, $actualValue, $outputReferenceValue);
		return $unitTestFailure;
	}
	
	/**
	 * 
	 * Returns a xml array as a new object for the unitTestFailure
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
