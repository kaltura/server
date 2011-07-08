<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/ThumbAssetServiceTestBase.php');

/**
 * thumbAsset service test case.
 */
class ThumbAssetServiceTest extends ThumbAssetServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($entryId, KalturaThumbAsset $thumbAsset, KalturaThumbAsset $reference)
	{
		parent::validateAdd($entryId, $thumbAsset, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource = null, KalturaThumbAsset $reference)
	{
		parent::validateUpdate($id, $thumbAsset, $contentResource, $reference);
		// TODO - add your own validations here
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
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbAssetListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($thumbAssetId)
	{
		parent::validateDelete($thumbAssetId);
		// TODO - add your own validations here
	}

}

