<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

//TODO: What will be our interface for the unit tests?
interface IUnitTest
{
	
}

/**
 * 
 * The Base class for all our unit tests, can be run in PHPUnit
 * @author Roni
 *
 */
class UnitTestBase extends PHPUnit_Framework_TestCase 
{
		
	/**
	 * 
	 * The unit test data provider (gets the data for the different unit tests)
	 * @param string $dataFilePath - the data file path (with the objects)
	 * @return array<array>();
	 */
	public function provider($dataFilePath)
	{
		try 
		{
			$simpleXML = simplexml_load_file($dataFilePath);
		}
		catch(Exception $e)
		{
			//TODO: exception handling
			print("Unable to load file : " . $dataFilePath. " as xml.\n Error: " . $e);
			die();
		}
		
		$inputsForUnitTests = array();
		
		foreach ($simpleXML->UnitTestsData->UnitTestData as $unitTestData)
		{
			$inputs = array();
			
			foreach ($simpleXML->UnitTestsData->UnitTestData->Inputs->Input as $input)
			{
				$object = kXml::XmlToObject($input);

				//Go to the last and current input and add the variable
				array_push($inputs, $object);

			}
			
			foreach ($simpleXML->UnitTestsData->UnitTestData->OutputReferences->OutputReference as $output)
			{
				$object = kXml::XmlToObject($output);

				//Go to the last and current input and add the variable
				array_push($inputs, $object);
			}
			
			$inputsForUnitTests[] = $inputs;
			
		}
		
		return $inputsForUnitTests; 
	}

	/**
	 * 
	 * Compares two propel objects using the first objects feilds (using getByNameMethod)
	 * @param BaseObject $outputReference
	 * @param BaseObject $newResult
	 * @return bool, if the objects are equal
	 */
	public function comparePropelObjectsByFields($outputReference, $newResult, $validErrorFields)
	{
		//Gets the data peer of the object (used to geting all the obejct feilds)
		$dataPeer = $outputReference->getPeer(); 
		
		//Gets all object feilds
		$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
		
		$isEqual = true;
		
		//Create the xml elements by all fields and their values
		foreach ($fields as $field)
		{
			//If the field is inthe valid error list then we skip him 
			if(in_array($field, $validErrorFields))
			{
				continue;
			}
			else 
			{
				$expectedValue = $outputReference->getByName($field);

				//if thisis an array we need to change it to a string
				if(is_array($expectedValue))
				{
					$expectedValue = implode(" , ", $expectedValue);
				}
				
				$actualValue = $newResult->getByName($field);
				
				//if this is an array we need to change it to a string
				if(is_array($actualValue))
				{
					$actualValue = implode(" , ", $actualValue);
				}
				
				if($expectedValue != $actualValue)
				{
					print("Error Output Reference value is: " . $expectedValue . " != actual output is: " . $actualValue ." on field " . $field . "\n");
					$isEqual = false;
				}
				else
				{
					//nothing to do here they are equal
				}
			}
		}
		
		return $isEqual;
	}
}

/**
 * 
 * Represents the base class for api_v3 unit tests
 * @author Roni
 *
 */
class Api_v3UnitTest extends UnitTestBase
{
	/**
	 * 
	 * Gets the parameters for creating a new kaltura client and returns the new client
	 * @param int $partnerId
	 * @param string $secret
	 * @param string $configServiceUrl
	 * @return KalturaClient - a new api client 
	 */
	public function getClient($partnerId, $secret, $configServiceUrl)
	{
		$config = new KalturaConfiguration((int)$partnerId);

		//Add the server url (into the test additional data)
		$config->serviceUrl = $configServiceUrl;
		$client = new KalturaClient($config);
		$ks = $client->session->start($secret, null, KalturaSessionType::ADMIN, (int)$partnerId, null, null);
		$client->setKs($ks);
		
		return $client;
	}
}
