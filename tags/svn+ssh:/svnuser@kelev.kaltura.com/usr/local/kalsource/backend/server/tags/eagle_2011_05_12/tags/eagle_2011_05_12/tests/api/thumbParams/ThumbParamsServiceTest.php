<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/ThumbParamsServiceBaseTest.php');

/**
 * thumbParams service test case.
 */
class ThumbParamsServiceTest extends ThumbParamsServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaThumbParams $thumbParams, KalturaThumbParams $reference)
	{
		parent::validateAdd($thumbParams, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaThumbParams $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaThumbParams $thumbParams, KalturaThumbParams $reference, $id)
	{
		parent::validateUpdate($thumbParams, $reference);
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
	protected function validateList(KalturaThumbParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests thumbParams->getByConversionProfileId action
	 * @param int $conversionProfileId
	 * @param KalturaThumbParamsArray $reference
	 * @dataProvider provideData
	 */
	public function testGetByConversionProfileId($conversionProfileId, KalturaThumbParamsArray $reference)
	{
		$resultObject = $this->client->thumbParams->getByConversionProfileId($conversionProfileId, $reference);
		$this->assertType('KalturaThumbParamsArray', $resultObject);
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
