<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/GenericDistributionProviderServiceBaseTest.php');

/**
 * genericDistributionProvider service test case.
 */
class GenericDistributionProviderServiceTest extends GenericDistributionProviderServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaGenericDistributionProvider $genericDistributionProvider, KalturaGenericDistributionProvider $reference)
	{
		parent::validateAdd($genericDistributionProvider, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaGenericDistributionProvider $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaGenericDistributionProvider $genericDistributionProvider, KalturaGenericDistributionProvider $reference, $id)
	{
		parent::validateUpdate($genericDistributionProvider, $reference);
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
	 * Validates testList results
	 */
	protected function validateList(KalturaGenericDistributionProviderFilter $filter = null, KalturaFilterPager $pager = null, KalturaGenericDistributionProviderListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
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
