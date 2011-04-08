<?php

/**
 * thumbAsset service base test case.
 */
abstract class ThumbAssetServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests thumbAsset->add action
	 * @param string $entryId 
	 * @param KalturaThumbAsset $thumbAsset 
	 * @param KalturaContentResource $contentResource 
	 * @param KalturaThumbAsset $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd($entryId, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->add($entryId, $thumbAsset, $contentResource);
		$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entryId, $thumbAsset, $contentResource, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($entryId, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource, KalturaThumbAsset $reference)
	{
	}

	/**
	 * Tests thumbAsset->update action
	 * @param string $id 
	 * @param KalturaThumbAsset $thumbAsset 
	 * @param KalturaContentResource $contentResource 
	 * @param KalturaThumbAsset $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->update($id, $thumbAsset, $contentResource);
		$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($id, $thumbAsset, $contentResource, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource, KalturaThumbAsset $reference)
	{
	}

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
	 * Tests thumbAsset->listAction action
	 * @param KalturaAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaThumbAssetListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbAssetListResponse $reference)
	{
		$resultObject = $this->client->thumbAsset->listAction($filter, $pager);
		$this->assertType('KalturaThumbAssetListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbAssetListResponse $reference)
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
