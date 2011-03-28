<?php

/**
 * syndicationFeed service base test case.
 */
abstract class SyndicationFeedServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests syndicationFeed->add action
	 * @param KalturaBaseSyndicationFeed $syndicationFeed 
	 * @param KalturaBaseSyndicationFeed $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
		$resultObject = $this->client->syndicationFeed->add($syndicationFeed);
		$this->assertType('KalturaBaseSyndicationFeed', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($syndicationFeed, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
	}

	/**
	 * Tests syndicationFeed->get action
	 * @param string $id 
	 * @param KalturaBaseSyndicationFeed $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaBaseSyndicationFeed $reference)
	{
		$resultObject = $this->client->syndicationFeed->get($id);
		$this->assertType('KalturaBaseSyndicationFeed', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($id, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaBaseSyndicationFeed $reference)
	{
	}

	/**
	 * Tests syndicationFeed->update action
	 * @param string $id 
	 * @param KalturaBaseSyndicationFeed $syndicationFeed 
	 * @param KalturaBaseSyndicationFeed $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
		$resultObject = $this->client->syndicationFeed->update($id, $syndicationFeed);
		$this->assertType('KalturaBaseSyndicationFeed', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($id, $syndicationFeed, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
	}

	/**
	 * Tests syndicationFeed->delete action
	 * @param string $id 
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->syndicationFeed->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests syndicationFeed->listAction action
	 * @param KalturaBaseSyndicationFeedFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaBaseSyndicationFeedListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseSyndicationFeedListResponse $reference)
	{
		$resultObject = $this->client->syndicationFeed->listAction($filter, $pager);
		$this->assertType('KalturaBaseSyndicationFeedListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseSyndicationFeedListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
