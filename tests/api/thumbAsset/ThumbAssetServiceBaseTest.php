<?php

/**
 * thumbAsset service base test case.
 */
abstract class ThumbAssetServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests thumbAsset->get action
	 * @param string $thumbAssetId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($thumbAssetId)
	{
		$resultObject = $this->client->thumbAsset->get($thumbAssetId);
		$this->assertType('KalturaThumbAsset', $resultObject);
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
	 * Tests thumbAsset->delete action
	 * @param string $thumbAssetId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($thumbAssetId)
	{
		$resultObject = $this->client->thumbAsset->delete($thumbAssetId);
	}

}
