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
	}

	/**
	 * Tests thumbAsset->generateByEntryId action
	 * @param string $entryId
	 * @param int $destThumbParamsId
	 * @dataProvider provideData
	 */
	public function testGenerateByEntryId($entryId, $destThumbParamsId)
	{
		$resultObject = $this->client->thumbAsset->generateByEntryId($entryId, $destThumbParamsId);
		$this->assertType('KalturaThumbAsset', $resultObject);
	}

	/**
	 * Tests thumbAsset->generate action
	 * @param string $entryId
	 * @param KalturaThumbParams $thumbParams
	 * @param string $sourceAssetId
	 * @dataProvider provideData
	 */
	public function testGenerate($entryId, KalturaThumbParams $thumbParams, $sourceAssetId = null)
	{
		$resultObject = $this->client->thumbAsset->generate($entryId, $thumbParams, $sourceAssetId);
		$this->assertType('KalturaThumbAsset', $resultObject);
	}

	/**
	 * Tests thumbAsset->regenerate action
	 * @param string $thumbAssetId
	 * @dataProvider provideData
	 */
	public function testRegenerate($thumbAssetId)
	{
		$resultObject = $this->client->thumbAsset->regenerate($thumbAssetId);
		$this->assertType('KalturaThumbAsset', $resultObject);
	}

	/**
	 * Tests thumbAsset->getByEntryId action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testGetByEntryId($entryId)
	{
		$resultObject = $this->client->thumbAsset->getByEntryId($entryId);
		$this->assertType('KalturaThumbAssetArray', $resultObject);
	}

	/**
	 * Tests thumbAsset->addFromUrl action
	 * @param string $entryId
	 * @param string $url
	 * @dataProvider provideData
	 */
	public function testAddFromUrl($entryId, $url)
	{
		$resultObject = $this->client->thumbAsset->addFromUrl($entryId, $url);
		$this->assertType('KalturaThumbAsset', $resultObject);
	}

	/**
	 * Tests thumbAsset->addFromImage action
	 * @param string $entryId
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testAddFromImage($entryId, file $fileData)
	{
		$resultObject = $this->client->thumbAsset->addFromImage($entryId, $fileData);
		$this->assertType('KalturaThumbAsset', $resultObject);
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
