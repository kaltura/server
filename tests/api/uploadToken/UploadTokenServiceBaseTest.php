<?php

/**
 * uploadToken service base test case.
 */
abstract class UploadTokenServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests uploadToken->add action
	 * @param KalturaUploadToken $uploadToken
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUploadToken $uploadToken = null)
	{
		$resultObject = $this->client->uploadToken->add($uploadToken);
		$this->assertType('KalturaUploadToken', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests uploadToken->get action
	 * @param string $uploadTokenId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($uploadTokenId)
	{
		$resultObject = $this->client->uploadToken->get($uploadTokenId);
		$this->assertType('KalturaUploadToken', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests uploadToken->delete action
	 * @param string $uploadTokenId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($uploadTokenId)
	{
		$resultObject = $this->client->uploadToken->delete($uploadTokenId);
	}

	/**
	 * Tests uploadToken->list action
	 * @param KalturaUploadTokenFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaUploadTokenFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->uploadToken->listAction($filter, $pager);
		$this->assertType('KalturaUploadTokenListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
