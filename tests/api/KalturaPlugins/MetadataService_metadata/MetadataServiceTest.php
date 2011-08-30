<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');
require_once(dirname(__FILE__) . '/MetadataServiceTestBase.php');

/**
 * metadata service test case.
 */
class MetadataServiceTest extends MetadataServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMetadataFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, KalturaMetadata $reference)
	{
		parent::validateAdd($metadataProfileId, $objectType, $objectId, $xmlData, $reference);
		// TODO - add your own validations here
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
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaMetadata $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, $xmlData = "", KalturaMetadata $reference)
	{
		parent::validateUpdate($id, $xmlData, $reference);
		// TODO - add your own validations here
	}

}

