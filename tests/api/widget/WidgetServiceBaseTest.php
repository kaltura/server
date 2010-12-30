<?php

/**
 * widget service base test case.
 */
abstract class WidgetServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests widget->add action
	 * @param KalturaWidget $widget
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaWidget $widget)
	{
		$resultObject = $this->client->widget->add($widget);
		$this->assertType('KalturaWidget', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests widget->update action
	 * @param string $id
	 * @param KalturaWidget $widget
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaWidget $widget)
	{
		$resultObject = $this->client->widget->update($id, $widget);
		$this->assertType('KalturaWidget', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests widget->get action
	 * @param string $id
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->widget->get($id);
		$this->assertType('KalturaWidget', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests widget->list action
	 * @param KalturaWidgetFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->widget->listAction($filter, $pager);
		$this->assertType('KalturaWidgetListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
