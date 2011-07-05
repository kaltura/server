<?php

require_once(dirname(__FILE__) . '/../../../../../../../tests/bootstrap.php');
require_once(dirname(__FILE__) . '/CaptionAssetServiceTestBase.php');

/**
 * captionAsset service test case.
 */
class CaptionAssetServiceTest extends CaptionAssetServiceTestBase
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
	protected function validateAdd($entryId, KalturaCaptionAsset $captionAsset, KalturaCaptionAsset $reference)
	{
		parent::validateAdd($entryId, $captionAsset, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCaptionAsset $captionAsset, KalturaContentResource $contentResource = null, KalturaCaptionAsset $reference)
	{
		parent::validateUpdate($id, $captionAsset, $contentResource, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($captionAssetId, KalturaCaptionAsset $reference)
	{
		parent::validateGet($captionAssetId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaCaptionAssetListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($captionAssetId)
	{
		parent::validateDelete($captionAssetId);
		// TODO - add your own validations here
	}

}

