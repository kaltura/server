<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UploadServiceBaseTest.php');

/**
 * upload service test case.
 */
class UploadServiceTest extends UploadServiceBaseTest
{
	/**
	 * Tests upload->upload action
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData)
	{
		$resultObject = $this->client->upload->upload($fileData);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests upload->getUploadedFileTokenByFileName action
	 * @param string $fileName
	 * @dataProvider provideData
	 */
	public function testGetUploadedFileTokenByFileName($fileName)
	{
		$resultObject = $this->client->upload->getUploadedFileTokenByFileName($fileName);
		$this->assertType('KalturaUploadResponse', $resultObject);
	}

}
