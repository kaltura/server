<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UploadTokenServiceBaseTest.php');

/**
 * uploadToken service test case.
 */
class UploadTokenServiceTest extends UploadTokenServiceBaseTest
{
	/**
	 * Tests uploadToken->upload action
	 * @param string $uploadTokenId
	 * @param file $fileData
	 * @param bool $resume
	 * @param bool $finalChunk
	 * @param int $resumeAt
	 * @dataProvider provideData
	 */
	public function testUpload($uploadTokenId, file $fileData, $resume = null, $finalChunk = 1, $resumeAt = -1)
	{
		$resultObject = $this->client->uploadToken->upload($uploadTokenId, $fileData, $resume, $finalChunk, $resumeAt);
		$this->assertType('KalturaUploadToken', $resultObject);
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
