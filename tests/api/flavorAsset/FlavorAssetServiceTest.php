<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorAssetServiceBaseTest.php');

/**
 * flavorAsset service test case.
 */
class FlavorAssetServiceTest extends FlavorAssetServiceBaseTest
{
	/**
	 * Tests flavorAsset->getByEntryId action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testGetByEntryId($entryId)
	{
		$resultObject = $this->client->flavorAsset->getByEntryId($entryId);
		$this->assertType('KalturaFlavorAssetArray', $resultObject);
	}

	/**
	 * Tests flavorAsset->getWebPlayableByEntryId action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testGetWebPlayableByEntryId($entryId)
	{
		$resultObject = $this->client->flavorAsset->getWebPlayableByEntryId($entryId);
		$this->assertType('KalturaFlavorAssetArray', $resultObject);
	}

	/**
	 * Tests flavorAsset->convert action
	 * @param string $entryId
	 * @param int $flavorParamsId
	 * @dataProvider provideData
	 */
	public function testConvert($entryId, $flavorParamsId)
	{
		$resultObject = $this->client->flavorAsset->convert($entryId, $flavorParamsId);
	}

	/**
	 * Tests flavorAsset->reconvert action
	 * @param string $id
	 * @dataProvider provideData
	 */
	public function testReconvert($id)
	{
		$resultObject = $this->client->flavorAsset->reconvert($id);
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

	/**
	 * Tests flavorAsset->getDownloadUrl action
	 * @param string $id
	 * @dataProvider provideData
	 */
	public function testGetDownloadUrl($id)
	{
		$resultObject = $this->client->flavorAsset->getDownloadUrl($id);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests flavorAsset->getFlavorAssetsWithParams action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testGetFlavorAssetsWithParams($entryId)
	{
		$resultObject = $this->client->flavorAsset->getFlavorAssetsWithParams($entryId);
		$this->assertType('KalturaFlavorAssetWithParamsArray', $resultObject);
	}

}
