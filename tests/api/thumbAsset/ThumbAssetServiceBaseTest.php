<?php

/**
 * thumbAsset service base test case.
 */
abstract class ThumbAssetServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests thumbAsset->get action
	 * @param string $thumbAssetId 
	 * @param KalturaThumbAsset $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($thumbAssetId, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->get($thumbAssetId);
		$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($thumbAssetId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($thumbAssetId, KalturaThumbAsset $reference)
	{
	}

	/**
	 * Tests thumbAsset->delete action
	 * @param string $thumbAssetId 
	 * @dataProvider provideData
	 */
	public function testDelete($thumbAssetId)
	{
		$resultObject = $this->client->thumbAsset->delete($thumbAssetId);
		$this->validateDelete($thumbAssetId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($thumbAssetId)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
