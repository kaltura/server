<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorAssetServiceBaseTest.php');

/**
 * flavorAsset service test case.
 */
class FlavorAssetServiceTest extends FlavorAssetServiceBaseTest
{
	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaFlavorAsset $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests flavorAsset->getByEntryId action
	 * @param string $entryId
	 * @param KalturaFlavorAssetArray $reference
	 * @dataProvider provideData
	 */
	public function testGetByEntryId($entryId, KalturaFlavorAssetArray $reference)
	{
		$resultObject = $this->client->flavorAsset->getByEntryId($entryId, $reference);
		$this->assertType('KalturaFlavorAssetArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests flavorAsset->getWebPlayableByEntryId action
	 * @param string $entryId
	 * @param KalturaFlavorAssetArray $reference
	 * @dataProvider provideData
	 */
	public function testGetWebPlayableByEntryId($entryId, KalturaFlavorAssetArray $reference)
	{
		$resultObject = $this->client->flavorAsset->getWebPlayableByEntryId($entryId, $reference);
		$this->assertType('KalturaFlavorAssetArray', $resultObject);
		// TODO - add here your own validations
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
		// TODO - add here your own validations
	}

	/**
	 * Tests flavorAsset->reconvert action
	 * @param string $id
	 * @dataProvider provideData
	 */
	public function testReconvert($id)
	{
		$resultObject = $this->client->flavorAsset->reconvert($id);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete($id);
		// TODO - add your own validations here
	}

	/**
	 * Tests flavorAsset->getDownloadUrl action
	 * @param string $id
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testGetDownloadUrl($id, $reference)
	{
		$resultObject = $this->client->flavorAsset->getDownloadUrl($id, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests flavorAsset->getFlavorAssetsWithParams action
	 * @param string $entryId
	 * @param KalturaFlavorAssetWithParamsArray $reference
	 * @dataProvider provideData
	 */
	public function testGetFlavorAssetsWithParams($entryId, KalturaFlavorAssetWithParamsArray $reference)
	{
		$resultObject = $this->client->flavorAsset->getFlavorAssetsWithParams($entryId, $reference);
		$this->assertType('KalturaFlavorAssetWithParamsArray', $resultObject);
		// TODO - add here your own validations
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
