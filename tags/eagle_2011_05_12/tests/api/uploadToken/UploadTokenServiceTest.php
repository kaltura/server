<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UploadTokenServiceBaseTest.php');

/**
 * uploadToken service test case.
 */
class UploadTokenServiceTest extends UploadTokenServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUploadToken $uploadToken = null, KalturaUploadToken $reference)
	{
		parent::validateAdd($uploadToken, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($uploadTokenId, KalturaUploadToken $reference)
	{
		parent::validateGet($uploadTokenId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests uploadToken->upload action
	 * @param string $uploadTokenId
	 * @param file $fileData
	 * @param bool $resume
	 * @param bool $finalChunk
	 * @param int $resumeAt
	 * @param KalturaUploadToken $reference
	 * @dataProvider provideData
	 */
	public function testUpload($uploadTokenId, file $fileData, $resume = null, $finalChunk = 1, $resumeAt = -1, KalturaUploadToken $reference)
	{
		$resultObject = $this->client->uploadToken->upload($uploadTokenId, $fileData, $resume, $finalChunk, $resumeAt, $reference);
		$this->assertType('KalturaUploadToken', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($uploadTokenId)
	{
		parent::validateDelete($uploadTokenId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaUploadTokenFilter $filter = null, KalturaFilterPager $pager = null, KalturaUploadTokenListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
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
