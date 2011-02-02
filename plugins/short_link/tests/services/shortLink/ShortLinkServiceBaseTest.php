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
	 * @param string $id 
	 * @param KalturaShortLink $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaShortLink $reference)
	{
		$resultObject = $this->client->shortLink->get($id);
		$this->assertType('KalturaShortLink', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($id, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaShortLink $reference)
	{
	}

	/**
	 * Tests shortLink->update action
	 * @param string $id 
	 * @param KalturaShortLink $shortLink 
	 * @param KalturaShortLink $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaShortLink $shortLink, KalturaShortLink $reference)
	{
		$resultObject = $this->client->shortLink->update($id, $shortLink);
		$this->assertType('KalturaShortLink', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($id, $shortLink, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaShortLink $shortLink, KalturaShortLink $reference)
	{
	}

	/**
	 * Tests shortLink->delete action
	 * @param string $id 
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->shortLink->delete($id);
		$this->validateDelete($id);
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
