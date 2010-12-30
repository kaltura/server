<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/WidgetServiceBaseTest.php');

/**
 * widget service test case.
 */
class WidgetServiceTest extends WidgetServiceBaseTest
{
	/**
	 * Tests widget->clone action
	 * @param KalturaWidget $widget
	 * @dataProvider provideData
	 */
	public function testClone(KalturaWidget $widget)
	{
		$resultObject = $this->client->widget->clone($widget);
		$this->assertType('KalturaWidget', $resultObject);
	}

}
