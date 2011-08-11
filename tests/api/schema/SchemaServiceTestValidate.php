<?php
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * schema service test case.
 */
class SchemaServiceTestValidate extends KalturaApiTestCase
{
	private $serviceUrl = 'http://devtests.kaltura.co.cc';
	
	private static $types = array(
		'bulkUploadXml.bulkUploadXML',
		'dropFolderXmlBulkUpload.dropFolderXml',
		'cuePoint.ingestAPI',
		'cuePoint.serveAPI',
		'syndication',
	);
	
// 	public function testXsd()
// 	{
// 		$serviceUrl = $this->client->getConfig()->serviceUrl;
// 		$serviceUrl = $this->serviceUrl;
		
// 		foreach(self::$types as $type)
// 		{
// 			echo "Testing XSD Type [$type]\n";
			
// 			$xsdPath = "$serviceUrl/api_v3/service/schema/action/serve/type/$type";
			
// 			$xsd = new DOMDocument();
// 			$xsd->load($xsdPath);
// 			$xsd->schemaValidate('http://www.w3.org/2001/XMLSchema.xsd');
// 		}
// 	}
	
	public function testXml()
	{
		foreach(self::$types as $type)
		{
			echo "Testing XML Type [$type]\n";
			
			$path = dirname(__FILE__) . "/testsData/$type";
			$this->assertFileExists($path);
			
			if(!is_dir($path))
				continue;
			
			$xmlFiles = scandir($path);
			foreach($xmlFiles as $xmlFile)
			{
				$xmlFile = realpath("$path/$xmlFile");
				if(!is_dir($xmlFile))
					$this->doTest($xmlFile, $type);
			}
		}
	}
	
	public function doTest($xmlPath, $type)
	{
		echo "	Testing File [$xmlPath]\n";
		$serviceUrl = $this->client->getConfig()->serviceUrl;
// 		$serviceUrl = $this->serviceUrl;
		$xsdPath = "$serviceUrl/api_v3/service/schema/action/serve/type/$type";
		
//		libxml_use_internal_errors(true);
//		libxml_clear_errors();
			
		$doc = new DOMDocument();
		$doc->Load($xmlPath);
		//Validate the XML file against the schema
		if(!$doc->schemaValidate($xsdPath)) 
		{
			$description = kXml::getLibXmlErrorDescription(file_get_contents($xmlPath));
			$this->fail("Type [$type] File [$xmlPath]: $description");
		}
	}
}

