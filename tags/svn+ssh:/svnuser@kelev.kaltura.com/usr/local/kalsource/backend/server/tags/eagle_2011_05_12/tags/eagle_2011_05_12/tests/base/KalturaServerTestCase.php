<?php

require_once (dirname(__FILE__). '/../bootstrap/bootstrapServer.php');

class KalturaServerTestCase extends KalturaTestCaseBase
{
	/**
	 * 
	 * Creates a new Kaltura test Object
	 * @param unknown_type $name
	 * @param array $data
	 * @param unknown_type $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * 
	 * Compares two propel objects and notifies the PHPUnit / Kaltura's listeners
	 * @param BaseObject $outputReference
	 * @param BaseObject $newResult
	 * @return array<> $newErrors, if the objects are equal
	 */
	public function comparePropelObjectsByFields($outputReference, $newResult, $validErrorFields)
	{
		//Gets the data peer of the object (used to geting all the obejct feilds)
		$dataPeer = $outputReference->getPeer(); 
		
		$outputReferenceId = $outputReference->getId();
		$newResultId = $newResult->getId();
		
		//Gets all object feilds
		$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
		
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
				$this->compareOnField($field, $actualValue, $expectedValue, "assertEquals");
			}
		}

		return $newErrors;
	}
}
