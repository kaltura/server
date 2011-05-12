<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/ThumbAssetServiceBaseTest.php');

/**
 * thumbAsset service test case.
 */
class ThumbAssetServiceTest extends ThumbAssetServiceBaseTest
{
	/**
	 * Tests thumbAsset->setAsDefault action
	 * @param string $thumbAssetId
	 * @dataProvider provideData
	 */
	public function testSetAsDefault($thumbAssetId)
	{
		$resultObject = $this->client->thumbAsset->setAsDefault($thumbAssetId);
		// TODO - add here your own validations
	}

	/**
	 * Tests thumbAsset->generateByEntryId action
	 * @param string $entryId
	 * @param int $destThumbParamsId
	 * @param KalturaThumbAsset $reference
	 * @dataProvider provideData
	 */
	public function testGenerateByEntryId($entryId, $destThumbParamsId, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->generateByEntryId($entryId, $destThumbParamsId, $reference);
		$this->assertType('KalturaThumbAsset', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests thumbAsset->generate action
	 * @param string $entryId
	 * @param KalturaThumbParams $thumbParams
	 * @param string $sourceAssetId
	 * @param KalturaThumbAsset $reference
	 * @dataProvider provideData
	 */
	public function testGenerate($entryId, KalturaThumbParams $thumbParams, $sourceAssetId = null, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->generate($entryId, $thumbParams, $sourceAssetId, $reference);
		$this->assertType('KalturaThumbAsset', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests thumbAsset->regenerate action
	 * @param string $thumbAssetId
	 * @param KalturaThumbAsset $reference
	 * @dataProvider provideData
	 */
	public function testRegenerate($thumbAssetId, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->regenerate($thumbAssetId, $reference);
		$this->assertType('KalturaThumbAsset', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($thumbAssetId, KalturaThumbAsset $reference)
	{
		parent::validateGet($thumbAssetId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests thumbAsset->getByEntryId action
	 * @param string $entryId
	 * @param KalturaThumbAssetArray $reference
	 * @dataProvider provideData
	 */
	public function testGetByEntryId($entryId, KalturaThumbAssetArray $reference)
	{
		$resultObject = $this->client->thumbAsset->getByEntryId($entryId, $reference);
		$this->assertType('KalturaThumbAssetArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests thumbAsset->addFromUrl action
	 * @param string $entryId
	 * @param string $url
	 * @param KalturaThumbAsset $reference
	 * @dataProvider provideData
	 */
	public function testAddFromUrl($entryId, $url, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->addFromUrl($entryId, $url, $reference);
		$this->assertType('KalturaThumbAsset', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests thumbAsset->addFromImage action
	 * @param string $entryId
	 * @param file $fileData
	 * @param KalturaThumbAsset $reference
	 * @dataProvider provideData
	 */
	public function testAddFromImage($entryId, file $fileData, KalturaThumbAsset $reference)
	{
		$resultObject = $this->client->thumbAsset->addFromImage($entryId, $fileData, $reference);
		$this->assertType('KalturaThumbAsset', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($thumbAssetId)
	{
		parent::validateDelete($thumbAssetId);
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
