<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/WidgetServiceTestBase.php');

/**
 * widget service test case.
 */
class WidgetServiceTest extends WidgetServiceTestBase
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
	protected function validateAdd(KalturaWidget $widget, KalturaWidget $reference)
	{
		parent::validateAdd($widget, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaWidget $widget, KalturaWidget $reference)
	{
		parent::validateUpdate($id, $widget, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaWidget $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null, KalturaWidgetListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

