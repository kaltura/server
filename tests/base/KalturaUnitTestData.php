<?php

require_once(dirname(__FILE__) . '/../bootstrap.php');

/**
 * 
 * Represents a single unit test data
 * @author Roni
 *
 */
class KalturaUnitTestData
{
	/**
	 * 
	 * The Test input
	 * @var array<unitTestDataObject>
	 */
	public $input = array();
	
	/**
	 * 
	 * The test output reference
	 * @var array<unitTestDataObject>
	 */
	public $outputReference = array();
	
	/**
	 * 
	 * Generates a new unitTestData object from a given simpleXLMElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * @return unitTestData - New unitTestData object
	 */
	public static function generateFromDataXml(SimpleXMLElement $simpleXMLElement)
	{
		$unitTestData = new KalturaUnitTestData();
		$unitTestData->fromDataXml($simpleXMLElement);
		return $unitTestData;
	}
	
	/**
	 * 
	 * Generates a new unitTestData object from a given simpleXLMElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * @return unitTestData - New unitTestData object
	 */
	public function fromDataXml(SimpleXMLElement $simpleXMLElement)
	{
		foreach ($simpleXMLElement->Inputs->Input as $input)
		{
			$unitTestDataObject = UnitTestDataObject::fromXml($input);
			$this->input[] = $unitTestDataObject;
		}
		
		foreach ($simpleXMLElement->OutputReferences->OutputReference as $outputReference)
		{
			$unitTestDataObject = UnitTestDataObject::fromXml($outputReference);
			$this->outputReference[] = $unitTestDataObject;
		}
	}
}