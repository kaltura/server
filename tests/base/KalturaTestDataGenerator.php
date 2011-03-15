<?php

require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');
require_once(dirname(__FILE__) . '/../bootstrap/bootstrapClient.php');

/**
 * Responsible for importing objects from kaltura DB 
 * and creating the tests data files 
 * @author Roni
 *
 */
class KalturaTestDataGenerator
{
	/**
	 * 
	 * The config file for the data importer
	 * @var KalturaTestDataSourceFile - the config file for the generator
	 */
	private $dataSourceFile = NULL;
					
	/**
	 * Creates a new Kaltura objects DataGenerator
	 * Gets The file path to it's configuration file
	 * 
	 * @param string $dataGeneratorConfigFilePath the config file path
	*/
	public function __construct($dataGeneratorConfigFilePath)
	{
		$simpleXMLElement = kXml::openXmlFile($dataGeneratorConfigFilePath);		
		$this->dataSourceFile = KalturaTestDataSourceFile::generateFromXML($simpleXMLElement);
		$this->dataSourceFile->setFilePath($dataGeneratorConfigFilePath);	
   	}
   
   	/**
   	 * Gets kaltura object id and type and return the needed Katura Object
   	 *  
   	 * @param string $objectId
   	 * @param string $objectType
   	 * @return KalturaTestDataObject - returns the test object with propel object from the DB
   	 */
   	private function getTestDataObject(KalturaTestDataObject $testObjectIdentifier)
   	{
   		$testDataObject = new KalturaTestDataObject($testObjectIdentifier->getType(), $testObjectIdentifier->getAdditionalData(), $testObjectIdentifier->getDataObject());
   		
   		$objectType = $testObjectIdentifier->getType();
   		$additionalData = $testObjectIdentifier->getAdditionalData();
   		
   		$objectId = $additionalData["key"];
   		
   		$testDataObject->setDataObject(KalturaTestDataGenerator::getObjectByTypeAndId($objectType, $objectId, $testDataObject->getAdditionalData()));
   		   			   		
   		return $testDataObject;
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
	private static function getAPIObject(KalturaObjectBase $objectInstance, $objectId, $partnerId, $secret, $serviceUrl, $service)
	{
		//here we create the KS and get the data from the API calls
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
	private static function getPropelObject(BaseObject $objectInstance, $objectId)
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
   	 * Creates a test file from the given KalturaTestCaseDataFile object (the data source for the test)
	 * @param testDataFile $testDataFile
	 */
	private function createTestDataFile(KalturaTestCaseDataFile $testDataSourceFile)
	{
		//TODO: break this function to 2 parts!
		
		//1. Create the new test data file
		$newTestDataFile = new KalturaTestCaseDataFile($testDataSourceFile->getTestCaseName());
				
	  	//2.For every test data we need to:
	   	foreach ($testDataSourceFile->getTestProceduresData() as $testProcedureData)
	   	{
	   		$newTestProcedureData = new KalturaTestProcedureData($testProcedureData->getProcedureName());
	   			   			   		
		   	foreach ($testProcedureData->getTestCasesData() as $testCaseData)
			{
				$newTestCaseData = new KalturaTestCaseInstanceData($testCaseData->getTestCaseInstanceName());
			 
				//1. Create the input and output reference objects
				$inputObjects = array();
				$outputReferenceObjects = array();
					
				//2. Foreach input - Get the object from kaltura DB and add it to the inputObjects array
				foreach ($testCaseData->getInput() as $inputIdentifier)
				{
					$inputObjects[] = $this->getTestDataObject($inputIdentifier);
				}
	
				//3. Foreach outputReference - Get the object from kaltura DB and add it to the outputReferenceObjects array
				foreach ($testCaseData->getOutputReference() as $outputReferenceIdentifier)
				{
					$outputReferenceObjects[] = $this->getTestDataObject($outputReferenceIdentifier);
				}
					
				//4. Create the new test data with the new objects 
				$newTestCaseData->setInput($inputObjects);
				$newTestCaseData->setOutputReference($outputReferenceObjects);
									
				//5. Add the new unit test data to the Data file
				$newTestProcedureData->addTestCaseInstance($newTestCaseData);
			}

			$newTestDataFile->addTestProcedureData($newTestProcedureData);	
	   	}
	   	
		chdir(dirname($this->dataSourceFile->getFilePath()));
				
		//3. Open the file at the file path 
		$testDatFileHandle = fopen("{$testDataSourceFile->getTestCaseName()}.data", "w+");
		
		//4. Convert the test data to xml dom
		$newTestDataDom = KalturaTestCaseDataFile::toXml($newTestDataFile);
		$newTestDataDom->formatOutput = true;

		//5. Save the entire test data file to the test data file name path (in XML)		
		fwrite($testDatFileHandle, $newTestDataDom->saveXML());
	}

	/**
	 * 
	 * Gets an object from propel or from API by given type, id and additionalData
	 * @param unknown_type $objectType - The object Type
	 * @param unknown_type $objectId - The objetc Id
	 * @param array<key=>value> $additionalData - Additional data is needed for api objects
	 * @return $objectType - The object (or null if no object exists) 
	 */
	private static function getObjectByTypeAndId($objectType, $objectId, array $additionalData = null)
	{
		$dataObject = null;
		
		//if the class exists
   		if(class_exists($objectType))
   		{	   		
   			//we create new object and by his type decide from where to get it (DB or API)
	   		$objectInstance = new $objectType;
	   		
	   		if($objectInstance InstanceOf BaseObject)
	   		{
	   			$dataObject = KalturaTestDataGenerator::getPropelObject($objectInstance, $objectId);
	   		}
	   		else if($objectInstance instanceof KalturaObject || $objectInstance instanceof KalturaObjectBase) // Object is Kaltura object (API)  
	   		{
	   			$partnerId = $additionalData["partnerId"];
	   			$secret = $additionalData["secret"];
	   			$serviceUrl = $additionalData["serviceUrl"];
	   			$service = $additionalData["service"];
	   			
	   			$dataObject = KalturaTestDataGenerator::getAPIObject($objectInstance, $objectId, $partnerId, $secret, $serviceUrl, $service);
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
   		
   		return $dataObject; 
	}
	
	/**
	 * 
	 * Creates the Tests data files 
	 * @retrun None, creates the tests data files (according to the config file data)
	 */
  	public function createTestDataFiles()
  	{
		//For each test found at the config fiel we need to create the test file
		foreach ($this->dataSourceFile->getTestFiles() as $testDataSourceFile)
		{
			$this->createTestDataFile($testDataSourceFile);
		}
	}
}