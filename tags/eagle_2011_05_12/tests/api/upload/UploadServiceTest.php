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
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData, $reference)
	{
		$resultObject = $this->client->upload->upload($fileData, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests upload->getUploadedFileTokenByFileName action
	 * @param string $fileName
	 * @param KalturaUploadResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetUploadedFileTokenByFileName($fileName, KalturaUploadResponse $reference)
	{
		$resultObject = $this->client->upload->getUploadedFileTokenByFileName($fileName, $reference);
		$this->assertType('KalturaUploadResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
