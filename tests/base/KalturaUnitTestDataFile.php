<?php
require_once(dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

	/**
	 * 
 * Represents a Test data file including couple of tests scenarios
 * @author Roni
 *
 */
class KalturaUnitTestDataFile
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
	 * @var array<KalturaUnitTestData>
	 */
	public $unitTestsData = array();
	
	/**
	 * 
	 * Generates a new testDataFile object from simpleXMLElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return testDataFile, new testDataFile object  
	 */
	public static function GeneratefromXML(SimpleXMLElement $simpleXMLElement)
	{
		$testDataFile = new KalturaUnitTestDataFile();
		$testDataFile->fromXML($simpleXMLElement);
		return $testDataFile;	
	}
	
	/**
	 * sets the testDataFile object from simpleXMLElement
	 * @param SimpleXMLElement $simpleXMLElement
	 * 
	 * @return None, sets the given object
	 */
	public function fromSourceXML(SimpleXMLElement $simpleXMLElement)
	{
		$this->fileName = trim((string)$simpleXMLElement->FileName);
								
		foreach ($simpleXMLElement->UnitTestsData->UnitTestData as $xmlUnitTestData)
		{
			$unitTestData = new KalturaUnitTestData();
				foreach ($xmlUnitTestData->Inputs->Input as $input)
				{
					$unitTestObjectIdentifier = new KalturaUnitTestDataObject(((string)$input["type"]), ((string)$input["key"]));
					$unitTestData->input[] = $unitTestObjectIdentifier;
				}
				
				foreach ($xmlUnitTestData->OutputReferences->OutputReference as $outputReference)
				{
					$unitTestObjectIdentifier = new KalturaUnitTestDataObject(((string)$outputReference["type"]), ((string)$outputReference["key"]));
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
	public function toDataXML()
	{
		//TODO: Add the objects header
		$dom = new DOMDocument("1.0");
		$dom->formatOutput = true;
		
		//Create elements in the Dom referencing the entire test data file
		$unitTestsElement = $dom->createElement("UnitTests");
		$dom->appendChild($unitTestsElement);
	
		$unitTestsDataElement = $dom->createElement("UnitTestsData");
		$unitTestsElement->appendChild($unitTestsDataElement);

		//For each unit test data
		foreach ($this->unitTestsData as $unitTestData)
		{
			//create all his elements
			$domUnitTestData = $dom->createElement("UnitTestData");
			$unitTestsDataElement->appendChild($domUnitTestData);

			$inputs = $dom->createElement("Inputs"); 
			$outputReferences = $dom->createElement("OutputReferences");
			
			$domUnitTestData->appendChild($inputs);
			$domUnitTestData->appendChild($outputReferences);
							
			//for each input:
			foreach ($unitTestData->input as $input)
			{
				//Create the xml from the object
				$objectAsDOM = KalturaUnitTestDataObject::toXml($input, "Input");
		 
				if($objectAsDOM->documentElement != NULL)
				{
					$importedNode = $dom->importNode($objectAsDOM->documentElement, true);
			
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
				$objectAsDOM = KalturaUnitTestDataObject::toXml($outputReference, "OutputReference");
		 
				if($objectAsDOM->documentElement != NULL)
				{
					$importedNode = $dom->importNode($objectAsDOM->documentElement, true);

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
	 * Generates a new testDatafile object from a given xml file path
	 * @param string $dataFilePath
	 * @return testDataFile - new TestDataFile object
	 */
	public static function generateFromDataXml($dataFilePath)
	{
		$testDataFile = new KalturaUnitTestDataFile();
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
		
		$this->fileName = $dataFilePath;
		
		foreach ($simpleXmlElement->UnitTestsData->UnitTestData as $unitTestDataXml)
		{
			$unitTestData = KalturaUnitTestData::generateFromDataXml($unitTestDataXml);
			$this->unitTestsData[] = $unitTestData;
		}
	}
}