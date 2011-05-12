<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/KalturaInternalToolsServiceBaseTest.php');

/**
 * KalturaInternalTools service test case.
 */
class KalturaInternalToolsServiceTest extends KalturaInternalToolsServiceBaseTest
{
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
