<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorParamsServiceBaseTest.php');

/**
 * flavorParams service test case.
 */
class FlavorParamsServiceTest extends FlavorParamsServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference)
	{
		parent::validateAdd($flavorParams, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaFlavorParams $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaFlavorParams $flavorParams, KalturaFlavorParams $reference, $id)
	{
		parent::validateUpdate($flavorParams, $reference);
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
	protected function validateList(KalturaFlavorParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests flavorParams->getByConversionProfileId action
	 * @param int $conversionProfileId
	 * @param KalturaFlavorParamsArray $reference
	 * @dataProvider provideData
	 */
	public function testGetByConversionProfileId($conversionProfileId, KalturaFlavorParamsArray $reference)
	{
		$resultObject = $this->client->flavorParams->getByConversionProfileId($conversionProfileId, $reference);
		$this->assertType('KalturaFlavorParamsArray', $resultObject);
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
