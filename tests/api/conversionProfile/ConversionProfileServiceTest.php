<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/ConversionProfileServiceTestBase.php');

/**
 * conversionProfile service test case.
 */
class ConversionProfileServiceTest extends ConversionProfileServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
		parent::validateAdd($conversionProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaConversionProfile $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
		parent::validateUpdate($id, $conversionProfile, $reference);
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
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaConversionProfileListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

