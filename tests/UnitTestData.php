<?php

	require_once('bootstrap.php');
	
	/**
	 * 
	 * Represent all kaltura object identifiers
	 * needed so we can get the object from the DB
	 * @author Roni
	 *
	 */
	class KalturaObjectIdentifier
	{
		/**
		 * Creates a new Kaltura Object Identifier
		 * @param $keys
		 * @param $type
		 */
		function __construct($type = null, $keys = null)
		{
			$this->type = $type;
			$this->keys = $keys;
		}
		
		/**
		 * the kaltura object type
		 */
		public $type;
		
		/**
		 * the object keys / key
		 * @var unknown_type
		 */
		public $keys = array();
	}

	/**
	 * 
	 * Represents a single unit test data
	 * @author Roni
	 *
	 */
	class UnitTestData
	{
		/**
		 * 
		 * The test data decription
		 * @var String
		 */
		public $description;
		
		/**
		 * 
		 * The Test input
		 * @var unknown_type
		 */
		public $input = array();
		
		/**
		 * 
		 * The test output reference
		 * @var unknown_type
		 */
		public $outputReference = array();
	}

	/**
	 * 
	 * Represents the data generator config file
	 * @author Roni
	 *
	 */
	class DataGeneratorConfigFile
	{
		/**
		 * 
		 * All the tests in the config file (each with file name and needed data)
		 * @var array<TestDataFile>
		 */
		public $tests = array();
		
		/**
		 * 
		 * Generates a new DataGeneratorConfigFile object from simpleXMLElement (the config file itself)
		 * @param SimpleXMLElement $simpleXMLElement
		 * 
		 * @return DataGeneratorConfigFile 
		 * 		   A new DataGeneratorConfigFile object  
		 */
		public static function generateFromXML(SimpleXMLElement $simpleXMLElement)
		{
			$dataGeneraotrConfigFile = new DataGeneratorConfigFile();
			$dataGeneraotrConfigFile->fromXML($simpleXMLElement);
			return $dataGeneraotrConfigFile;
		}
		
		/**
		 * sets the DataGeneratorConfigFile object from simpleXMLElement
		 * @param SimpleXMLElement $simpleXMLElement
		 * 
		 * @return None, sets the given object
		 */
		public function fromXML(SimpleXMLElement $simpleXMLElement)
		{
			//For each test file
			foreach ($simpleXMLElement->TestDataFile as $xmlTestDataFile)
			{
				//Create new test file obejct
				$testDataFile = new TestDataFile();
				
				//For each UnitTest data (in this file)
				foreach ($xmlTestDataFile->UnitTestsData->UnitTestData as $xmlUnitTestData)
				{
					//Create new unit test data
					$unitTestData = new UnitTestData();
					
					//For each input create the needed Kaltura object identifier
					foreach ($xmlUnitTestData->Inputs->Input as $input)
					{
						$kalturaObjectIdentifier = new KalturaObjectIdentifier(((string)$input["type"]), ((string)$input["key"]));
						$unitTestData->input[] = $kalturaObjectIdentifier;
					}
					
					//And for each output reference create the needed kaltura object identifier
					foreach ($xmlUnitTestData->OutputReferences->OutputReference as $outputReference)
					{
						$kalturaObjectIdentifier = new KalturaObjectIdentifier(((string)$outputReference["type"]), ((string)$outputReference["key"]));
						$unitTestData->outputReference[] = $kalturaObjectIdentifier;		
					}
				}
				
				$testDataFile->fileName = ((string)$xmlTestDataFile->fileName);
				$testDataFile->unitTestsData[] = $unitTestData;
				$this->tests[] = $testDataFile;
			} 
		}
	}
	
	/**
	 * 
	 * Represents a Test data file including couple of tests scenarios
	 * @author Roni
	 *
	 */
	class TestDataFile
	{
		/**
		 * 
		 * The test file name
		 * @var string
		 */
		public $fileName;
		
		/**
		 * 
		 * All the file unit tests data
		 * @var array<UnitTestData>
		 */
		public $unitTestsData = array();
		
		/**
		 * 
		 * Generates a new TestDataFile object from simpleXMLElement
		 * @param SimpleXMLElement $simpleXMLElement
		 * 
		 * @return A new TestDataFile object  
		 */
		public static function GeneratefromXML(SimpleXMLElement $simpleXMLElement)
		{
			$testDataFile = new TestDataFile();
			$testDataFile->fromXML($simpleXMLElement);
			return $testDataFile;	
		}
		
		/**
		 * sets the TestDataFile object from simpleXMLElement
		 * @param SimpleXMLElement $simpleXMLElement
		 * 
		 * @return None, sets the given object
		 */
		public function fromXML(SimpleXMLElement $simpleXMLElement)
		{
			$this->fileName = $simpleXMLElement->fileName[0];
//			$this->unitTestsData = new UnitTestsData();
			foreach ($simpleXMLElement->UnitTestsData->UnitTestData as $xmlUnitTestData)
			{
				$unitTestData = new UnitTestData();
					foreach ($xmlUnitTestData->Inputs->Input as $input)
					{
						$kalturaObjectIdentifier = new KalturaObjectIdentifier(((string)$input["type"]), ((string)$input["key"]));
						$unitTestData->input[] = $kalturaObjectIdentifier;
					}
					
					foreach ($xmlUnitTestData->OutputReferences->OutputReference as $outputReference)
					{
						$kalturaObjectIdentifier = new KalturaObjectIdentifier(((string)$outputReference["type"]), ((string)$outputReference["key"]));
						$unitTestData->outputReference[] = $kalturaObjectIdentifier;		
					}
												
				$this->unitTestsData[] = $unitTestData;
			} 
		}
		
		/**
		 * 
		 * Returns the test data file in XML format 
		 * @return string XML encoded representation of the test data file object
		 */
		public function toXML()
		{
			return "TODO Parse the test file object TO XML"; 
		}
	}

	/**
	 * 
	 * The object type Enum (used to identify the tested objects)
	 * @author Roni
	 *
	 */
	class KalturaObjectType
	{
		const Entry = "Entry";
		const MediaInfo = "MediaInfo";
		const FlavorParams = "FlavorParams";
		const FlavorParamsOutput = "FlavorParamsOutput";
	}
 
	$dataGeneratorConfigFile = simplexml_load_file("c:/Users/Roni/DataGeneratorConfigFile.config");
	$dataGeneratorConfigFile = DataGeneratorConfigFile::generateFromXML($dataGeneratorConfigFile); 
	print_r($dataGeneratorConfigFile);
	
	
//	$test1 = simplexml_load_file("c:/Users/Roni/SimpleXMLElement.data");
//	
//	//	print($test1->asXML());
//	 
//	$testDataFile = TestDataFile::GeneratefromXML($test1);
////	print_r($testDataFile);
////	
////	print("\n Inputs!!! \n");
////	print_r($testDataFile->unitTestsData[0]->input);
////	
////	print("\n OutputReferneces!!! \n");
////	print_r($testDataFile->unitTestsData[0]->outputReference[0]);
//
//foreach ($testDataFile->unitTestsData as $unitTestData)
//{
//	print("New Unit Test");
//	
//	foreach ($unitTestData->input as $input)
//	{
//		print("New input");
//		print_r($input);
//	}
//	
//	foreach ($unitTestData->outputReference as $outputReference)
//	{
//		print("New outputReference");
//		print_r($outputReference);			
//	}
//}
//	print($testDataFile->unitTestsData[0]->outputReference[0])
?>