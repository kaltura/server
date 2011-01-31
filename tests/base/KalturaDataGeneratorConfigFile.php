<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents the data generator config file 
 * that holds all the data that needs to be imported
 * @author Roni
 *
 */
class KalturaDataGeneratorConfigFile
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
	 * Generates a new KalturaDataGeneratorConfigFile object from simpleXMLElement (the config file itself)
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return KalturaDataGeneratorConfigFile 
	 * 		   A new KalturaDataGeneratorConfigFile object  
	 */
	public static function generateFromXML(SimpleXMLElement $simpleXMLElement)
	{
		$dataGeneratorConfigFile = new KalturaDataGeneratorConfigFile();
		$dataGeneratorConfigFile->fromSourceXML($simpleXMLElement);
		return $dataGeneratorConfigFile;
	}
	
	/**
	 * sets the KalturaDataGeneratorConfigFile object from simpleXMLElement (the source xml of the data)
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
			$testDataFile = new KalturaUnitTestDataFile();
			
			//For each UnitTest data (in this file)
			foreach ($xmlTestDataFile->UnitTestsData->UnitTestData as $xmlUnitTestData)
			{
				//Create new unit test data
				$unitTestData = new KalturaUnitTestData();

				//For each input create the needed Kaltura object identifier
				foreach ($xmlUnitTestData->Inputs->Input as $input)
				{
					$additionalData = kXml::getAttributesAsArray($input);
					$unitTestDataObjectIdentifier = new KalturaUnitTestDataObject(((string)$input["type"]), $additionalData);
					$unitTestData->input[] = $unitTestDataObjectIdentifier;
				}
				
				//And for each output reference create the needed kaltura object identifier
				foreach ($xmlUnitTestData->OutputReferences->OutputReference as $outputReference)
				{
					$additionalData = kXml::getAttributesAsArray($outputReference);
					$unitTestDataObjectIdentifier = new KalturaUnitTestDataObject(((string)$outputReference["type"]), $additionalData);
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
