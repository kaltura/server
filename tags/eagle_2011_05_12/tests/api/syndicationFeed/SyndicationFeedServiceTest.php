<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/SyndicationFeedServiceBaseTest.php');

/**
 * syndicationFeed service test case.
 */
class SyndicationFeedServiceTest extends SyndicationFeedServiceBaseTest
{
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
	 * Validates testList results
	 */
	protected function validateList(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseSyndicationFeedListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests syndicationFeed->getEntryCount action
	 * @param string $feedId
	 * @param KalturaSyndicationFeedEntryCount $reference
	 * @dataProvider provideData
	 */
	public function testGetEntryCount($feedId, KalturaSyndicationFeedEntryCount $reference)
	{
		$resultObject = $this->client->syndicationFeed->getEntryCount($feedId, $reference);
		$this->assertType('KalturaSyndicationFeedEntryCount', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests syndicationFeed->requestConversion action
	 * @param string $feedId
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testRequestConversion($feedId, $reference)
	{
		$resultObject = $this->client->syndicationFeed->requestConversion($feedId, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
