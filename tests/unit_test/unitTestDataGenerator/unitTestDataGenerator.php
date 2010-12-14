<?php

require_once(dirname(__FILE__) . '/../bootstrap.php');

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
		$this->generatorConfigFile->filePath = $dataGeneratorConfigFilePath;	
   	}
   
   	/**
   	 * Gets kaltura object id and type and return the needed Katura Object
   	 *  
   	 * @param string $objectId
   	 * @param string $objectType
   	 * @return BaseObject - returns the propel object from the DB
   	 */
   	private function getUnitTestDataObject(UnitTestDataObject $unitTestObjectIdentifier)
   	{
   		$unitTestDataObject = new UnitTestDataObject($unitTestObjectIdentifier->type, $unitTestObjectIdentifier->additionalData, $unitTestObjectIdentifier->dataObject);
   		
   		$objectType = $unitTestObjectIdentifier->type;
   		
   		//if the class exists
   		if(class_exists($objectType ))
   		{	   		
   			//we create new object and by his type decide from where to get it (DB or API)
	   		$objectInstance = new $objectType;
	   		
	   		$objectId = $unitTestObjectIdentifier->additionalData["key"];
	   		
	   		if($objectInstance InstanceOf BaseObject)
	   		{
	   			$unitTestDataObject->dataObject = $this->getPropelObject($objectInstance, $objectId);
	   		}
	   		else if($objectInstance instanceof KalturaObject || $objectInstance instanceof KalturaObjectBase) // Object is Kaltura object (API)  
	   		{
	   			$partnerId = $unitTestObjectIdentifier->additionalData["partnerId"];
	   			$secret = $unitTestObjectIdentifier->additionalData["secret"];
	   			$serviceUrl = $unitTestObjectIdentifier->additionalData["serviceUrl"];
	   			$service = $unitTestObjectIdentifier->additionalData["service"];
	   			
	   			$unitTestDataObject->dataObject = $this->getAPIObject($objectInstance, $objectId, $partnerId, $secret, $serviceUrl, $service);
	   		}
	   		else // normal objects types like string and int 
	   		{
	   			throw new Exception("What type am i? \n" , var_dump($objectInstance)); 
	   		} 
   		}
   		else
   		{
   			//For unknown types we just copy the xml row as is
   			//TODO: add support for fileData object... create it from the file path given...
   			
   		}
   			   		
   		return $unitTestDataObject;
	}
	   	
	/**
	 * 
	 * Gets an API object from the API - using a client session and KS also using the action get from this service on the object id
	 * 
	 * @param KalturaObjectBase $objectInstance
	 * @param string $objectId
	 * @param int $partnerId
	 * @param string $secret
	 * @param string $serviceUrl
	 * @param string $service
	 * @throws KalturaAPIException
	 */
	private function getAPIObject(KalturaObjectBase $objectInstance, $objectId, $partnerId, $secret, $serviceUrl, $service)
	{
		//here we create the KS and get the data from the API calls
		//TODO: how to get the right service from the $objectInstace or type or more info is needed?
		
		$config = new KalturaConfiguration((int)$partnerId);
		$config->serviceUrl = $serviceUrl;
		$client = new KalturaClient($config);
		$ks = $client->session->start($secret, null, KalturaSessionType::ADMIN, (int)$partnerId, null, null);
		$client->setKs($ks);
		
		$entryId = $objectId;
		$result = $client->$service->get($entryId);
		
		return $result;
	}
		
	/**
	 * 
	 * Gets a propel object from the DB
	 * @param BaseObject $objectInstance
	 * @param string $objectId
	 * @throws Exception
	 */
	private function getPropelObject(BaseObject $objectInstance, $objectId)
	{
		if(method_exists($objectInstance, 'getPeer'))
	   	{
	   		$peer = $objectInstance->getPeer();
	   	}
	   	else
	   	{
	   		//TODO: exception handling
	   		throw new Exception("Can't locate object's peer - the object doesn't have getPeer method. Object class = " . get_class($objectInstance));
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
					
		//1.1 Add the description for this file
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
				$inputObjects[] = $this->getUnitTestDataObject($inputIdentifier);
			}
									
			//3. Foreach outputReference - Get the object from kaltura DB and add it to the outputReferenceObjects array
			foreach ($unitTestData->outputReference as $outputReferenceIdentifier)
			{
				$outputReferenceObjects[] = $this->getUnitTestDataObject($outputReferenceIdentifier);
			}
				
			//4. Create the new unit test data with the new objects 
				
			$unitTestDataObjects->input = $inputObjects;
			$unitTestDataObjects->outputReference = $outputReferenceObjects;
								
			//5. Add the new unit test data to the Data file
			$newTestDataFile->unitTestsData[] = $unitTestDataObjects;
		}

		chdir(dirname($this->generatorConfigFile->filePath));
				
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
  	public function createTestFiles()
  	{
		//For each test found at the config fiel we need to create the test file
		foreach ($this->generatorConfigFile->testFiles as $testFile)
		{
			$this->createTestFile($testFile);
		}
	}
}