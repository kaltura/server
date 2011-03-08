<?php

require_once(dirname(__FILE__) . '/../../../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/UiConfAdminServiceBaseTest.php');

/**
 * uiConfAdmin service test case.
 */
class UiConfAdminServiceTest extends UiConfAdminServiceBaseTest
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
