<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UiConfServiceBaseTest.php');

/**
 * uiConf service test case.
 */
class UiConfServiceTest extends UiConfServiceBaseTest
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests uiConf->clone action
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testClone($id)
	{
		$resultObject = $this->client->uiConf->clone($id);
		$this->assertType('KalturaUiConf', $resultObject);
	}

	/**
	 * Tests uiConf->listTemplates action
	 * @param KalturaUiConfFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListTemplates(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->uiConf->listTemplates($filter, $pager);
		$this->assertType('KalturaUiConfListResponse', $resultObject);
	}

}
