<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/SyndicationFeedServiceBaseTest.php');

/**
 * syndicationFeed service test case.
 */
class SyndicationFeedServiceTest extends SyndicationFeedServiceBaseTest
{
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
	 * Tests syndicationFeed->getEntryCount action
	 * @param string $feedId
	 * @dataProvider provideData
	 */
	public function testGetEntryCount($feedId)
	{
		$resultObject = $this->client->syndicationFeed->getEntryCount($feedId);
		$this->assertType('KalturaSyndicationFeedEntryCount', $resultObject);
	}

	/**
	 * Tests syndicationFeed->requestConversion action
	 * @param string $feedId
	 * @dataProvider provideData
	 */
	public function testRequestConversion($feedId)
	{
		$resultObject = $this->client->syndicationFeed->requestConversion($feedId);
		$this->assertType('string', $resultObject);
	}

}
