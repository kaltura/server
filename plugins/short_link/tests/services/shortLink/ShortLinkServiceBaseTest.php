<?php

/**
 * shortLink service base test case.
 */
abstract class ShortLinkServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests shortLink->list action
	 * @param KalturaShortLinkFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaShortLinkListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaShortLinkFilter $filter = null, KalturaFilterPager $pager = null, KalturaShortLinkListResponse $reference)
	{
		$resultObject = $this->client->shortLink->list($filter, $pager);
		$this->assertType('KalturaShortLinkListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaShortLinkFilter $filter = null, KalturaFilterPager $pager = null, KalturaShortLinkListResponse $reference)
	{
	}

	/**
	 * Tests shortLink->add action
	 * @param KalturaShortLink $shortLink 
	 * @param KalturaShortLink $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaShortLink $shortLink, KalturaShortLink $reference)
	{
		$resultObject = $this->client->shortLink->add($shortLink);
		$this->assertType('KalturaShortLink', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($shortLink, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaShortLink $shortLink, KalturaShortLink $reference)
	{
	}

	/**
	 * Tests shortLink->get action
	 * @param KalturaShortLink $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaShortLink $reference, $id)
	{
		$resultObject = $this->client->shortLink->get($id);
		$this->assertType('KalturaShortLink', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaShortLink $reference, $id)
	{
	}

	/**
	 * Tests shortLink->update action
	 * @param KalturaShortLink $shortLink 
	 * @param KalturaShortLink $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaShortLink $shortLink, KalturaShortLink $reference, $id)
	{
		$resultObject = $this->client->shortLink->update($id, $shortLink);
		$this->assertType('KalturaShortLink', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($shortLink, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaShortLink $shortLink, KalturaShortLink $reference, $id)
	{
	}

	/**
	 * Tests shortLink->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->shortLink->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
