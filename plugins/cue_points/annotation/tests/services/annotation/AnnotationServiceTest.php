<?php

require_once(dirname(__FILE__) . '/../../../../../../../../tests/bootstrap.php');
require_once(dirname(__FILE__) . '/AnnotationServiceTestBase.php');

/**
 * annotation service test case.
 */
class AnnotationServiceTest extends AnnotationServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null, KalturaCuePointListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaCuePoint $cuePoint, KalturaCuePoint $reference)
	{
		parent::validateAdd($cuePoint, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaCuePoint $reference)
	{
		parent::validateGet($id, $reference);
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
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCuePoint $cuePoint, KalturaCuePoint $reference)
	{
		parent::validateUpdate($id, $cuePoint, $reference);
		// TODO - add your own validations here
	}

}

