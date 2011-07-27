<?php
require_once(dirname(__FILE__) . '/../../bootstrap/bootstrapAPI.php');

/**
 * The BulkUploadServeLogTest unit test case
 * tests api v3 
 * service: bulk upload 
 * action: serve log 
 * @author Guyba
 *
 */
class BulkUploadServeLogTest extends KalturaApiTestCase
{
	public $name = "BulkUploadServeLogTest";
	
	/**
	 * 
	 * Creates a new BulkUploadServeLog Test case
	 * @param string $name
	 * @param array<unknown_type> $data
	 * @param string $dataName
	 */
	public function __construct($name = "BulkUploadServeLogTest", array $data = array(), $dataName ="Default data")
	{
		parent::__construct($name, $data, $dataName);
	}
	
	/**
	 * Test the serve log action for bulk upload XML
	 * @param int $id
	 * @param string $xmlLog
	 * @dataProvider provideData
	 */
	public function testServeLog($id, $xmlLog)
	{
		$urlToActualXmlLog= $this->client->bulkUpload->serveLog($id);
		$actualXmlLog = self::getApiFileFromUrl($urlToActualXmlLog);
		print("Result : " . print_r($actualXmlLog, true) . "\n");
		$this->assertEquals($xmlLog, $actualXmlLog);
//		$this->compareOnField("xmlLog", $result , $xmlLog, 'assertEquals');
	}
}