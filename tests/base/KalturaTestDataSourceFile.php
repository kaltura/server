<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents the data generator config file 
 * that holds all the data that needs to be imported
 * @author Roni
 *
 */
class KalturaTestDataSourceFile
{
	/**
	 * 
	 * The config file path
	 * @var string
	 */
	private $filePath;
	
	/**
	 * 
	 * All the tests in the config file (each with file name and needed data)
	 * @var array<KalturaTestCaseDataFile>
	 */
	private $testFiles = array();
	
	/**
	 * @return the $filePath
	 */
	public function getFilePath() {
		return $this->filePath;
	}

	/**
	 * @return the $testFiles
	 */
	public function getTestFiles() {
		return $this->testFiles;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/**
	 * @param array<KalturaTestClassDataFile> $testFiles
	 */
	public function setTestFiles($testFiles) {
		$this->testFiles = $testFiles;
	}

	/**
	 * 
	 * Generates a new KalturaTestDataSourceFile object from simpleXMLElement (the config file itself)
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return KalturaTestDataSourceFile 
	 * 		   A new KalturaTestDataSourceFile object  
	 */
	public static function generateFromXML(SimpleXMLElement $simpleXMLElement)
	{
		$dataGeneratorConfigFile = new KalturaTestDataSourceFile();
		$dataGeneratorConfigFile->fromSourceXML($simpleXMLElement);
		return $dataGeneratorConfigFile;
	}
	
	/**
	 * sets the KalturaTestDataSourceFile object from simpleXMLElement (the source xml of the data)
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return None, sets the given object
	 */
	public function fromSourceXML(SimpleXMLElement $simpleXMLElement)
	{
		//For each test file
 		foreach ($simpleXMLElement->TestCaseData as $xmlTestDataFile)
		{
			//Create new test file obejct
			$testDataFile = new KalturaTestCaseDataFile(trim((string)$xmlTestDataFile["testCaseName"]));
			
			foreach ($xmlTestDataFile->TestProcedureData as $xmlTestProcedureData)
			{
				$testProcedureData = new KalturaTestProcedureData();
				
				if(isset($xmlTestProcedureData["testProcedureName"]))
				{
					$testProcedureData->setProcedureName($xmlTestProcedureData["testProcedureName"]); 
				}

				//TODO: maybe get this from procedure / test case instance config
				$testCaseNum = 0;
				
				//For each test data (in this file)
				foreach ($xmlTestProcedureData->TestCaseData as $xmlTestCaseData)
				{
					$testCaseName = $testProcedureData->getProcedureName() . " with data set #{$testCaseNum}";
					$testCaseNum++;
					
					//User defined test cases name
					if(isset($xmlTestCaseData["testCaseInstanceName"]))
					{
						$testCaseName = $xmlTestCaseData["testCaseInstanceName"];
						$testCaseNum--;
					}
					
					//Create new unit test data
					$testCaseData = new KalturaTestCaseInstanceData($testCaseName);
										
					//For each input create the needed Kaltura object identifier
					foreach ($xmlTestCaseData->Input as $input)
					{
						$additionalData = kXml::getAttributesAsArray($input);
						$testInputDataObjectIdentifier = new KalturaTestDataObject(((string)$input["type"]), $additionalData);
						$testCaseData->addInput($testInputDataObjectIdentifier);
					}
					
					//And for each output reference create the needed kaltura object identifier
					foreach ($xmlTestCaseData->OutputReference as $outputReference)
					{
						$additionalData = kXml::getAttributesAsArray($outputReference);
						$testOutputReferenceDataObjectIdentifier = new KalturaTestDataObject(((string)$outputReference["type"]), $additionalData);
						$testCaseData->addOutputReference($testOutputReferenceDataObjectIdentifier);		
					}
									
					//Add the new test case data into the test procedure data.
					$testProcedureData->addTestCaseInstance($testCaseData);
				}
				
				//Add the new procedure test data into the test file.
				$testDataFile->addTestProcedureData($testProcedureData);
			}
			
			$this->testFiles[] = $testDataFile;		
		}
	}
}
