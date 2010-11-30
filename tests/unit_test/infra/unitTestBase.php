<?php


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
class unitTestBase extends PHPUnit_Framework_TestCase 
{
	/**
	 * 
	 * The feilds that are ok to be invalid such as UpdatedAt / CreatedAt (skipped fields)
	 * @var array<string> the feilds names
	 */
	public $validErrorFields = array();
	
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
	public function comparePropelObjectsByFeilds($outputReference, $newResult)
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
				if(in_array($field, $this->validErrorFields))
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

?>