<?php

/**
 * syndicationFeed service base test case.
 */
abstract class SyndicationFeedServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests syndicationFeed->add action
	 * @param KalturaBaseSyndicationFeed $syndicationFeed
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaBaseSyndicationFeed $syndicationFeed)
	{
		$resultObject = $this->client->syndicationFeed->add($syndicationFeed);
		$this->assertType('KalturaBaseSyndicationFeed', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests syndicationFeed->get action
	 * @param string $id
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->syndicationFeed->get($id);
		$this->assertType('KalturaBaseSyndicationFeed', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests syndicationFeed->update action
	 * @param string $id
	 * @param KalturaBaseSyndicationFeed $syndicationFeed
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaBaseSyndicationFeed $syndicationFeed)
	{
		$resultObject = $this->client->syndicationFeed->update($id, $syndicationFeed);
		$this->assertType('KalturaBaseSyndicationFeed', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests syndicationFeed->delete action
	 * @param string $id
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->syndicationFeed->delete($id);
	}

	/**
	 * Tests syndicationFeed->list action
	 * @param KalturaBaseSyndicationFeedFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->syndicationFeed->listAction($filter, $pager);
		$this->assertType('KalturaBaseSyndicationFeedListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
