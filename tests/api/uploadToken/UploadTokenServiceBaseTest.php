<?php

/**
 * uploadToken service base test case.
 */
abstract class UploadTokenServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests uploadToken->add action
	 * @param KalturaUploadToken $uploadToken 
	 * @param KalturaUploadToken $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUploadToken $uploadToken = null, KalturaUploadToken $reference)
	{
		$resultObject = $this->client->uploadToken->add($uploadToken);
		$this->assertType('KalturaUploadToken', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($uploadToken, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUploadToken $uploadToken = null, KalturaUploadToken $reference)
	{
	}

	/**
	 * Tests uploadToken->get action
	 * @param string $uploadTokenId 
	 * @param KalturaUploadToken $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($uploadTokenId, KalturaUploadToken $reference)
	{
		$resultObject = $this->client->uploadToken->get($uploadTokenId);
		$this->assertType('KalturaUploadToken', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($uploadTokenId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($uploadTokenId, KalturaUploadToken $reference)
	{
	}

	/**
	 * Tests uploadToken->delete action
	 * @param string $uploadTokenId 
	 * @dataProvider provideData
	 */
	public function testDelete($uploadTokenId)
	{
		$resultObject = $this->client->uploadToken->delete($uploadTokenId);
		$this->validateDelete($uploadTokenId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($uploadTokenId)
	{
	}

	/**
	 * Tests uploadToken->list action
	 * @param KalturaUploadTokenFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUploadTokenListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaUploadTokenFilter $filter = null, KalturaFilterPager $pager = null, KalturaUploadTokenListResponse $reference)
	{
		$resultObject = $this->client->uploadToken->list($filter, $pager);
		$this->assertType('KalturaUploadTokenListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaUploadTokenFilter $filter = null, KalturaFilterPager $pager = null, KalturaUploadTokenListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
