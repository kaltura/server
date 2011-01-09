<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

/**
 * 
 * Represents the data generator config file 
 * that holds all the data that needs to be imported
 * @author Roni
 *
 */
class dataGeneratorConfigFile
{
	/**
	 * 
	 * The config file path
	 * @var string
	 */
	public $filePath;
	
	/**
	 * 
	 * All the tests in the config file (each with file name and needed data)
	 * @var array<testDataFile>
	 */
	public $testFiles = array();
	
	/**
	 * 
	 * Generates a new dataGeneratorConfigFile object from simpleXMLElement (the config file itself)
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return dataGeneratorConfigFile 
	 * 		   A new dataGeneratorConfigFile object  
	 */
	public static function generateFromXML(SimpleXMLElement $simpleXMLElement)
	{
		$dataGeneratorConfigFile = new dataGeneratorConfigFile();
		$dataGeneratorConfigFile->fromSourceXML($simpleXMLElement);
		return $dataGeneratorConfigFile;
	}
	
	/**
	 * sets the dataGeneratorConfigFile object from simpleXMLElement (the source xml of the data)
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return None, sets the given object
	 */
	public function fromSourceXML(SimpleXMLElement $simpleXMLElement)
	{
		//For each test file
		foreach ($simpleXMLElement->TestDataFile as $xmlTestDataFile)
		{
			//Create new test file obejct
			$testDataFile = new unitTestDataFile();
			
			//For each UnitTest data (in this file)
			foreach ($xmlTestDataFile->UnitTestsData->UnitTestData as $xmlUnitTestData)
			{
				//Create new unit test data
				$unitTestData = new unitTestData();

				//For each input create the needed Kaltura object identifier
				foreach ($xmlUnitTestData->Inputs->Input as $input)
				{
					$additionalData = kXml::getAttributesAsArray($input);
					$unitTestDataObjectIdentifier = new UnitTestDataObject(((string)$input["type"]), $additionalData);
					$unitTestData->input[] = $unitTestDataObjectIdentifier;
				}
				
				//And for each output reference create the needed kaltura object identifier
				foreach ($xmlUnitTestData->OutputReferences->OutputReference as $outputReference)
				{
					$additionalData = kXml::getAttributesAsArray($outputReference);
					$unitTestDataObjectIdentifier = new UnitTestDataObject(((string)$outputReference["type"]), $additionalData);
					$unitTestData->outputReference[] = $unitTestDataObjectIdentifier;		
				}
				
				//Add the new unit test into the tests array.
				$testDataFile->unitTestsData[] = $unitTestData;
			}
			
			$testDataFile->fileName = trim((string)$xmlTestDataFile->FileName);
													
			$this->testFiles[] = $testDataFile;
		}
	}
}
