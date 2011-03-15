<?php
require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * Represents a Test data file including couple of tests scenarios
 * @author Roni
 *
 */
class KalturaTestCaseDataFile
{
	/**
	 * 
	 * Creates a new test case data file
	 * @param string $testCaseName
	 * @param array $testProceduresData
	 */
	public function __construct($testCaseName = "", array $testProceduresData = array())
	{
		$this->testCaseName = $testCaseName;
		$this->testProceduresData = $testProceduresData;
	}
	
	/**
	 * 
	 * The test file name
	 * @var string
	 */
	private $testCaseName;
	
	/**
	 * 
	 * All the file tests data
	 * @var array<KalturaTestProcedureData>
	 */
	private $testProceduresData = array();
	
	/**
	 * @return the $testCaseName
	 */
	public function getTestCaseName() {
		return $this->testCaseName;
	}

	/**
	 * @return the $testProceduresData
	 */
	public function getTestProceduresData() {
		return $this->testProceduresData;
	}

	/**
	 * @param string $testCaseName
	 */
	public function setTestCaseName($testCaseName) {
		$this->testCaseName = $testCaseName;
	}

	/**
	 * @param array<KalturaTestProcedureData> $testProceduresData
	 */
	public function setTestProceduresData(array $testProceduresData) {
		$this->testProceduresData = $testProceduresData;
	}

	/**
	 * 
	 * Add a KalturaTestProcedureData into the test case procedures data
	 * @param KalturaTestProcedureData $testProcedureData
	 */	
	public function addTestProcedureData(KalturaTestProcedureData $testProcedureData)
	{
		if($this->testProceduresData == null)
		{
			$this->testProceduresData = array();
		}
		
		$this->testProceduresData[] = $testProcedureData;
	}
	
	
	/**
	 * Sets the testDataFile object from simpleXMLElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return None, sets the given object
	 */
	public function fromSourceXML(SimpleXMLElement $simpleXMLElement)
	{
		$this->testCaseName = trim((string)$simpleXMLElement["testCaseName"]);
								
		foreach ($simpleXMLElement->TestProcedureData as $xmlTestProcedure)
		{
			$testProcedure = new KalturaTestProcedureData($xmlTestProcedure["testProcedureName"]);
			
			foreach ($xmlTestProcedure->TestCaseData as $xmlTestCaseInstanceData)
			{
				$testCaseInstanceData = new KalturaTestCaseInstanceData();
				$testDataInput = array();
				$testDataOutputReference = array();
				
				foreach ($xmlUnitTestData->Input as $input)
				{
					$testObjectIdentifier = new KalturaTestDataObject(((string)$input["type"]), ((string)$input["key"]));
					
					$testDataInput[] = $testObjectIdentifier;
				}
								
				foreach ($xmlUnitTestData->OutputReference as $outputReference)
				{
					$testObjectIdentifier = new KalturaTestDataObject(((string)$outputReference["type"]), ((string)$outputReference["key"]));
					$testCaseOutputReference = $testObjectIdentifier;		
				}
				
				$testCaseInstanceData->setInput($testDataInput);
				$testCaseInstanceData->setOutputReference($testCaseOutputReference);
				$testProcedure->addTestCaseInstance($testCaseInstanceData);
			}
						
			$this->testProceduresData[] = $testProcedure;
		}
		
	}

	/**
	 * 
	 * Returns the given KalturaTestDataFile as DomDocument
	 * @param KalturaTestDataFile $testDataFile
	 */
	public static function toXml(KalturaTestCaseDataFile $testDataFile)
	{
		$dom = new DOMDocument("1.0");
		
		//Create elements in the Dom referencing the entire test data file
		$testCaseDataElement = $dom->createElement("TestCaseData");
		$testCaseDataElement->setAttribute("testCaseName", $testDataFile->getTestCaseName());
		$dom->appendChild($testCaseDataElement);
	
		//For each test procedure data
		foreach ($testDataFile->getTestProceduresData() as $testProcedureData)
		{
			$domTestProcedureData = KalturaTestProcedureData::toXml($testProcedureData);
			kXml::appendDomToElement($domTestProcedureData, &$testCaseDataElement, $dom);
		}

		return $dom;
	}
	
	/**
	 * 
	 * Generates a new testDatafile object from a given xml file path
	 * @param string $dataFilePath
	 * @return testDataFile - new TestDataFile object
	 */
	public static function generateFromDataXml($dataFilePath)
	{
		$testDataFile = new KalturaTestCaseDataFile();
		$testDataFile->fromDataXml($dataFilePath);
		return $testDataFile;
	}
	
	/**
	 * 
	 * Sets the object from a given data xml
	 */
	public function fromDataXml($dataFilePath)
	{
		$simpleXmlElement = kXml::openXmlFile($dataFilePath);
		
		$this->testCaseName = (string)$simpleXmlElement["testCaseName"];
		
		foreach ($simpleXmlElement->TestProcedureData as $xmlTestProcedureData)
		{
			$testProcedureData = KalturaTestProcedureData::generateFromDataXml($xmlTestProcedureData);
						
			$this->testProceduresData[$testProcedureData->getProcedureName()] = $testProcedureData;
		}
	}
}