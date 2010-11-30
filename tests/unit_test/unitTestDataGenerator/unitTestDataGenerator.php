<?php

require_once('tests/unit_test/bootstrap.php');

	/**
    * Responsible for importing objects from kaltura DB 
    * and creating the unit tests data files 
	* @author Roni
	*
	*/
	class UnitTestDataGenerator
	{
		/**
		 * 
		 * The config file for the data importer
		 * @var DataGeneratorConfigFile - the config file for the generator
		 */
		private $generatorConfigFile = NULL;
						
		/**
		 * Creates a new Kaltura objects DataGenerator
		 * Gets The file path to it's configuration file
		 * 
		 * @param string $dataGeneratorConfigFilePath the config file path
		*/
		public function __construct($dataGeneratorConfigFilePath)
		{
			$simpleXMLElement = simplexml_load_file($dataGeneratorConfigFilePath);
			$this->generatorConfigFile = DataGeneratorConfigFile::generateFromXML($simpleXMLElement);	
	   	}
   
	   	/**
	   	 * Gets kaltura object id and type and return the needed Katura Object
	   	 *  
	   	 * @param $objectId
	   	 * @param $objectType
	   	 * @return BaseObject - returns the propel object from the DB
	   	 */
	   	function getKalturaObjectByTypeAndId($objectType, $objectId)
	   	{
	   		$kalturaObject = null;
	   		
	   		//if the class exists
	   		if(class_exists($objectType))
	   		{	   		
	   			//we create new object and gets his peer
		   		$objectInstance = new $objectType();
		   		if(method_exists($objectInstance, 'getPeer'))
		   		{
		   			$peer = $objectInstance->getPeer();
		   		}
		   		else
		   		{
		   			//TODO: exception handling
		   			throw ("Can't locate objects peer - the object doesn't have getPeer method" . $objectInstance);
		   		}
	   		}
	   		else
	   		{
	   			//TODO: exception handling
	   			throw ("Can't create object,");
	   		}
	   		
	   		//we retrive the object by his id
	   		$kalturaObject = $peer->retrieveByPK($objectId);
	   		
	   		return $kalturaObject;
		}
	   	
	   	/**
	   	 * 
	   	 * Creates a test file from the given TestDataFile object
	   	 * @param TestDataFile $testDataFile
	   	 */
	  	private function createTestFile($testDataFile)
	   	{
	   		//1. Create the new unit test data file  
	   		$newTestDataFile = new TestDataFile();
					
	   		//1.5 Add the description for this file
	   		$newTestDataFile->description = $testDataFile->description;
	   		
	   		//2.For every unit test data we need to:	   		   		
		   	foreach ($testDataFile->unitTestsData as $unitTestData)
			{
				$unitTestDataObjects = new UnitTestData();
				
				//0. add the test description to the test file
				$unitTestDataObjects->description = $unitTestData->description;
				 
				//1. Create the input and output reference objects
				$inputObjects = array();
				$outputReferenceObjects = array();
				
				//2. Foreach input - Get the object from kaltura DB and add it to the inputObjects array
				foreach ($unitTestData->input as $inputIdentifier)
				{
					$inputObjects[] = $this->getKalturaObjectByTypeAndId($inputIdentifier->type, $inputIdentifier->keys);
				}
									
				//3. Foreach outputReference - Get the object from kaltura DB and add it to the outputReferenceObjects array
				foreach ($unitTestData->outputReference as $outputReferenceIdentifier)
				{
					$outputReferenceObjects[] = $this->getKalturaObjectByTypeAndId($outputReferenceIdentifier->type, $outputReferenceIdentifier->keys);
				}
				
				//4. Create the new unit test data with the new objects 
				
				$unitTestDataObjects->input = $inputObjects;
				$unitTestDataObjects->outputReference = $outputReferenceObjects;
								
				//5. Add the new unit test data to the Data file
				$newTestDataFile->unitTestsData[] = $unitTestDataObjects;
			}

			//3. Open the file at the file path 
			$unitTestDatFileHandle = fopen($testDataFile->fileName, "w+");
			
			//4. Save the entire test data file to the test data file name path (in XML)
			fwrite($unitTestDatFileHandle, $newTestDataFile->toXML());
		}
   		
		/**
		 * 
		 * Creates the Tests data files 
		 * @retrun None, creates the tests data files (according to the config file data)
		 */
   		function createTestFiles()
   		{
   			//For each test found at the config fiel we need to create the test file
			foreach ($this->generatorConfigFile->tests as $test)
			{
				$this->createTestFile($test);
			}
		}
	}
	
?>