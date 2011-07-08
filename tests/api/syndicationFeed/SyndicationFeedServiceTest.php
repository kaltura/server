<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/SyndicationFeedServiceTestBase.php');

/**
 * syndicationFeed service test case.
 */
class SyndicationFeedServiceTest extends SyndicationFeedServiceTestBase
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
	protected function validateAdd(KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
		parent::validateAdd($syndicationFeed, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaBaseSyndicationFeed $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
		parent::validateUpdate($id, $syndicationFeed, $reference);
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
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseSyndicationFeedListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

