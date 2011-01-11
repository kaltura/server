<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/UiConfServiceBaseTest.php');

/**
 * uiConf service test case.
 */
class UiConfServiceTest extends UiConfServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
		parent::validateAdd($uiConf, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaUiConf $uiConf, KalturaUiConf $reference, $id)
	{
		parent::validateUpdate($uiConf, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaUiConf $reference, $id)
	{
		parent::validateGet($reference);
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
	 * Tests uiConf->clone action
	 * @param KalturaUiConf $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testClone(KalturaUiConf $reference, $id)
	{
		$resultObject = $this->client->uiConf->clone($id, $reference);
		$this->assertType('KalturaUiConf', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests uiConf->listTemplates action
	 * @param KalturaUiConfFilter $filter
	 * @param KalturaFilterPager $pager
	 * @param KalturaUiConfListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListTemplates(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfListResponse $reference)
	{
		$resultObject = $this->client->uiConf->listTemplates($filter, $pager, $reference);
		$this->assertType('KalturaUiConfListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfListResponse $reference)
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
