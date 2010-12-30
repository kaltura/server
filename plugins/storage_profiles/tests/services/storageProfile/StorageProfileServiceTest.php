<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/StorageProfileServiceBaseTest.php');

/**
 * storageProfile service test case.
 */
class StorageProfileServiceTest extends StorageProfileServiceBaseTest
{
	/**
	 * Tests storageProfile->listByPartner action
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testListByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->storageProfile->listByPartner($filter, $pager);
		$this->assertType('KalturaStorageProfileListResponse', $resultObject);
	}

	/**
	 * Tests storageProfile->updateStatus action
	 * @param int $storageId
	 * @param KalturaStorageProfileStatus $status
	 * @dataProvider provideData
	 */
	public function testUpdateStatus($storageId, KalturaStorageProfileStatus $status)
	{
		$resultObject = $this->client->storageProfile->updateStatus($storageId, $status);
	}

}
