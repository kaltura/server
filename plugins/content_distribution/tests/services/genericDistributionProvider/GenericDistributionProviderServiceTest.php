<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/GenericDistributionProviderServiceBaseTest.php');

/**
 * genericDistributionProvider service test case.
 */
class GenericDistributionProviderServiceTest extends GenericDistributionProviderServiceBaseTest
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testList with data set #0
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
