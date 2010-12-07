<?php

	chdir(dirname(__FILE__));
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
		 * The config file path
		 * @var string
		 */
		public $filePath;
		
//		/**
//		 * 
//		 * Represents if the file is in relative path
//		 * @var bool
//		 */
//		public $isRelative = false;
		 
		/**
		 * 
		 * All the tests in the config file (each with file name and needed data)
		 * @var array<TestDataFile>
		 */
		public $testFiles = array();
		
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
				
				//Add the test file: description and file name
				$testDataFile->description = trim((string)$xmlTestDataFile->Description);
				$testDataFile->fileName = trim((string)$xmlTestDataFile->FileName);
				
//				$isRelativeAttribute = kXml::getXmlAttributeAsString($xmlTestDataFile->FileName, "type");
//				$testDataFile->isRelative = $isRelativeAttribute == "relative";
														
				$this->testFiles[] = $testDataFile;
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
		 * The test data file header 
		 * @var unknown_type
		 */
		public $header; 
		
		/**
		 * 
		 * The test file name
		 * @var string
		 */
		public $fileName;
		
//		/**
//		 * Represend if the file path is relative
//		 * @var bool
//		 */
//		public $isRelative;
		
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
						$unitTestObjectIdentifier = new UnitTestDataObject(((string)$input["type"]), ((string)$input["key"]));
						$unitTestData->input[] = $unitTestObjectIdentifier;
					}
					
					foreach ($xmlUnitTestData->OutputReferences->OutputReference as $outputReference)
					{
						$unitTestObjectIdentifier = new UnitTestDataObject(((string)$outputReference["type"]), ((string)$outputReference["key"]));
						$unitTestData->outputReference[] = $unitTestObjectIdentifier;		
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
						
//						$objectType = $this->getObjectType($input);
//						$objectId = $this->getObjectId($input);	
//																	
//						$importedNode->setAttribute("type", $objectType);	
//						
//						if(class_exists($objectType))
//						{
//							$importedNode->setAttribute("key", $objectId);
//						}
//						else
//						{
//							$importedNode->setAttribute("value", $objectId);
//						}
						
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
						
//						$objectType = $this->getObjectType($outputReference);
//						$objectId = $this->getObjectId($outputReference);
//						
//						$importedNode->setAttribute("type", $objectType);		
//						$importedNode->setAttribute("key", $objectId);

						//Add him to the output reference elements
						$outputReferences->appendChild($importedNode);
					}
					else
					{
						//Object is null so we make only an empty node!
						throw new Exception("One of the objects is null : " . var_dump($outputReference));
					}
				}
				
				$unitTestsDataElement->appendChild($domUnitTestData);
			}

			//return the XML well formated
			$dom->formatOutput = true;
						
			return $dom->saveXML();
		}
		
		/**
		 * 
		 * gets an object and returns his id
		 * @param unknown_type $object
		 * @return string - the given object Id  
		 */
		private function getObjectId($object)
		{
			if($object instanceof BaseObject)
			{
				$objectId = $object->getByName("Id");	
			}
			else if ($object instanceof KalturaObject || $object instanceof KalturaObjectBase)
			{
				//TODO: check if all kaltura objects are supported
				$reflector = new ReflectionObject($object);
				$idProperty  = $reflector->getProperty("id");
				$objectId = $idProperty->getValue($object);
			}
			else 
			{
				$objectId = $object;
			}
				
			return $objectId;
		}

				/**
		 * 
		 * gets an object and returns his type or class name
		 * @param unknown_type $object
		 * @return string - the given object type  
		 */
		private function getObjectType($object)
		{
			if($object instanceof BaseObject)
			{
				$objectType = get_class($object);	
			}
			else if ($object instanceof KalturaObject || $object instanceof KalturaObjectBase)
			{
				//TODO: check if all kaltura objects are supported
				$objectType = get_class($object);
			}
			else 
			{
				$objectType = gettype($object);
			}
				
			return $objectType;
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
	 * needed so we can get the object from the DB / API
	 * @author Roni
	 *
	 */
	class UnitTestDataObject
	{
		/**
		 * Creates a new Kaltura Object Identifier
		 * @param $keys
		 * @param $type
		 */
		function __construct($type = null, $additionalData = null, $dataObject = null)
		{
			$this->type = $type;
			$this->additionalData = $additionalData;
			$this->dataObject = $dataObject;
		}
		
		/**
		 * the kaltura object type
		 */
		public $type;
		
		/**
		 * 
		 * Additional data for the object identifier (such as key, partnerId, secret, value)
		 * @var array<key => value>
		 */
		public $additionalData = array();
		
		/**
		 * 
		 * The data object to be retrieved like propel or kaltura object
		 * @var unknown_type
		 */
		public $dataObject;
	}
