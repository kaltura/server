<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/WidgetServiceBaseTest.php');

/**
 * widget service test case.
 */
class WidgetServiceTest extends WidgetServiceBaseTest
{
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
	 * Tests widget->clone action
	 * @param KalturaWidget $widget
	 * @param KalturaWidget $reference
	 * @dataProvider provideData
	 */
	public function testClone(KalturaWidget $widget, KalturaWidget $reference)
	{
		$resultObject = $this->client->widget->clone($widget, $reference);
		$this->assertType('KalturaWidget', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null, KalturaWidgetListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
