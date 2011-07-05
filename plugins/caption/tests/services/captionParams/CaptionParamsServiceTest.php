<?php

require_once(dirname(__FILE__) . '/../../../../../../../tests/bootstrap.php');
require_once(dirname(__FILE__) . '/CaptionParamsServiceTestBase.php');

/**
 * captionParams service test case.
 */
class CaptionParamsServiceTest extends CaptionParamsServiceTestBase
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
	protected function validateAdd(KalturaCaptionParams $captionParams, KalturaCaptionParams $reference)
	{
		parent::validateAdd($captionParams, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaCaptionParams $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCaptionParams $captionParams, KalturaCaptionParams $reference)
	{
		parent::validateUpdate($id, $captionParams, $reference);
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
	protected function validateListAction(KalturaCaptionParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaCaptionParamsListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

