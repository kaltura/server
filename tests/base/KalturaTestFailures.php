<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

/**
 * 
 * All the tests failures
 * @author Roni
 *
 */
class KalturaTestFailures
{
	/**
	 * 
	 * Creates a new test failures (for entire run of tests)
	 */
	function __construct()
	{
		$this->testCaseFailures = array();
	}
	
	/**
	 * 
	 * Holds all the tests failures
	 * @var array<KalturaUnitTestCaseFailure>
	 */
	public $testCaseFailures = array();
	
	/**
	 * 
	 * Returns all the failures as XML formatted string
	 * @throws Exception
	 */
	public function toXML()
	{
		if(count($this->testCaseFailures) == 0)
		{
			return "";
		}
		
		//TODO: Add the objects header
		$dom = new DOMDocument("1.0");
		$dom->formatOutput = true;
		
		//Create elements in the Dom referencing the entire test data file
		$unitTestsElement = $dom->createElement("UnitTests");
		$dom->appendChild($unitTestsElement);
		
		$failuresElement = $dom->createElement("Failures");

		//For each unit test data
		foreach ($this->testCaseFailures as $testCaseFailure)
		{
			//Create the xml from the object
			$objectAsDOM = KalturaTestCaseFailure::toXml($testCaseFailure, "UnitTestFailures");
		 
			if($objectAsDOM->documentElement != NULL)
			{
				$importedNode = $dom->importNode($objectAsDOM->documentElement, true);

				//Add him to the input elements
				$failuresElement->appendChild($importedNode);
			}
			else
			{
				//Object is null so we make only an empty node!
				throw new Exception("One of the objects is null : " . $testCaseFailure);
			}
		}
			
		$unitTestsElement->appendChild($failuresElement );

		//return the XML well formated
		$dom->formatOutput = true;
					
		return $dom->saveXML();
	}
	
	/**
	 * 
	 * Generates a new KalturaTestsFailures object from a given failure file path 
	 * @param string $failureFilePath
	 */
	public static function generateFromXml($failureFilePath)
	{
		$testsFailures = new KalturaTestFailures();
		$testsFailures->fromXml($failureFilePath);
		return $testsFailures;		
	}
	
	/**	
	 * 
	 * Generates a new KalturaTestsFailures object from a given failure file path 
	 * @param string $failureFilePath
	 */
	public function fromXml($failureFilePath)
	{
		$simpleXML = kXml::openXmlFile($failureFilePath);

		foreach ($simpleXML->Failures->UnitTestFailures as $unitTestFailureXml)
		{
			$this->testCaseFailures[] = KalturaTestCaseFailure::generateFromXml($unitTestFailureXml);
						
		}
	}
}