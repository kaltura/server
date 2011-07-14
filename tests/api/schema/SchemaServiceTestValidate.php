<?php
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * schema service test case.
 */
class SchemaServiceTestValidate extends KalturaApiTestCase
{

	public function testXsd()
	{
		$type = 'bulkUploadXml.bulkUploadXML';
		$serviceUrl = $this->client->getConfig()->serviceUrl;
		$xsdPath = "$serviceUrl/api_v3/service/schema/action/serve/type/$type";
		
		$xsd = new DOMDocument();
		$xsd->load($xsdPath);
		$xsd->schemaValidate('http://www.w3.org/2001/XMLSchema.xsd');
	}
	
	public function testXml()
	{
		$this->doTest('testsData/bulk_upload.1.xml', KalturaSchemaType::BULK_UPLOAD_XML);
	}
	
	public function doTest($xmlPath, $type)
	{
		$serviceUrl = $this->client->getConfig()->serviceUrl;
		$xsdPath = "$serviceUrl/api_v3/service/schema/action/serve/type/$type";
		
//		libxml_use_internal_errors(true);
//		libxml_clear_errors();
			
		$doc = new DOMDocument();
		$doc->Load($xmlPath);
		//Validate the XML file against the schema
		if(!$doc->schemaValidate($xsdPath)) 
		{
			$description = kXml::getLibXmlErrorDescription(file_get_contents($xmlPath));
			
	        throw new Exception($description);
		}
	}
}

