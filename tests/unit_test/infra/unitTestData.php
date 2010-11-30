<?php

	require_once('../bootstrap.php');
	
	/**
	 * 
	 * Represents the data generator config file 
	 * that holds all the data that needs to be imported
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
					$unitTestData = new unitTestData();
					
					//Add the test description
					$unitTestData->description = trim((string)$xmlUnitTestData->Description);
					
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
				
				//Add the test file: description and file name
				$testDataFile->description = trim((string)$xmlTestDataFile->Description);
				$testDataFile->fileName = trim((string)$xmlTestDataFile->FileName);
				
				//Add the new unit test into the tests array.
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
		 * The test data file description
		 * @var string
		 */
		public $description;
		
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
		 * @return TestDataFile, new TestDataFile object  
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
			$this->fileName = trim((string)$simpleXMLElement->FileName);

			$this->description = trim((string)$simpleXMLElement->Description);
			
			foreach ($simpleXMLElement->UnitTestsData->UnitTestData as $xmlUnitTestData)
			{
				$unitTestData = new unitTestData();
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
		 * Returns the test data file in XML format (including the objects) 
		 * @return string XML encoded representation of the test data file object
		 */
		public function toXML()
		{
			//TODO: Add the objects header
			$dom = new DOMDocument("1.0");
			$dom->formatOutput = true;
			
			//Create elements in the Dom referencing the entire test data file
			$unitTestsElement = $dom->createElement("UnitTests");
			$dom->appendChild($unitTestsElement);
		
			$testsDescriptionNode = $dom->createElement("Description", $this->description);	
			$unitTestsElement->appendChild($testsDescriptionNode);
			
			$unitTestsDataElement = $dom->createElement("UnitTestsData");
			$unitTestsElement->appendChild($unitTestsDataElement);
	
			//For each unit test data
			foreach ($this->unitTestsData as $unitTestData)
			{
				//create all his elements
				$domUnitTestData = $dom->createElement("UnitTestData");
				$unitTestsDataElement->appendChild($domUnitTestData);

				$descriptionNode = $dom->createElement("Description", $unitTestData->description);
				$domUnitTestData->appendChild($descriptionNode);

				$inputs = $dom->createElement("Inputs"); 
				$outputReferences = $dom->createElement("OutputReferences");
				
				$domUnitTestData->appendChild($inputs);
				$domUnitTestData->appendChild($outputReferences);
								
				//for each input:
				foreach ($unitTestData->input as $input)
				{
					//Create the xml from the object
					$objectAsDOM = kXml::objectToXml($input, "Input");
			 
					if($objectAsDOM->documentElement != NULL)
					{
						$importedNode = $dom->importNode($objectAsDOM->documentElement, true);
						
						$objectType = get_class($input);
						
						$importedNode->setAttribute("type", $objectType);		
						$importedNode->setAttribute("key", $input->getId());

						//Add him to the input elements
						$inputs->appendChild($importedNode);
					}
					else
					{
						//Object is null so we make only an empty node!
						throw new Exception("One of the objects is null : " . $input);
					}
				}
				
				//for each outputReference:
				foreach ($unitTestData->outputReference as $outputReference)
				{
					//Create the xml from the object
					$objectAsDOM = kXml::ObjectToXml($outputReference, "OutputReference");
			 
					if($objectAsDOM->documentElement != NULL)
					{
						$importedNode = $dom->importNode($objectAsDOM->documentElement, true);
						
						$objectType = get_class($outputReference);
						
						$importedNode->setAttribute("type", $objectType);		
						$importedNode->setAttribute("key", $outputReference->getId());

						//Add him to the output reference elements
						$outputReferences->appendChild($importedNode);
					}
					else
					{
						//Object is null so we make only an empty node!
						throw new Exception("One of the objects is null : " . $outputReference);
					}
				}
				
				$unitTestsDataElement->appendChild($domUnitTestData);
			}

			//return the XML well formated
			$dom->formatOutput = true;
						
			return $dom->saveXML();
		}
	}
	
		/**
	 * 
	 * Represents a single unit test data
	 * @author Roni
	 *
	 */
	class unitTestData
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
	 * Represent all propel object identifiers
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

?>