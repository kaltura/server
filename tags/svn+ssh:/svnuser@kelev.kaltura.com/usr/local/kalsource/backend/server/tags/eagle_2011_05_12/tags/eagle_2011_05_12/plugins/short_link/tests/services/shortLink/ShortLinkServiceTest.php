<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/ShortLinkServiceBaseTest.php');

/**
 * shortLink service test case.
 */
class ShortLinkServiceTest extends ShortLinkServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaShortLinkFilter $filter = null, KalturaFilterPager $pager = null, KalturaShortLinkListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaShortLink $shortLink, KalturaShortLink $reference)
	{
		parent::validateAdd($shortLink, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaShortLink $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaShortLink $shortLink, KalturaShortLink $reference, $id)
	{
		parent::validateUpdate($shortLink, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
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
