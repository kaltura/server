<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');

/**
 * EntryDistributionService test case.
 */
class EntryDistributionServiceTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests EntryDistributionService->addAction()
	 * @param KalturaEntryDistribution $entryDistribution
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEntryDistribution $entryDistribution)
	{
		try
		{
			$resultEntryDistribution = $this->client->entryDistribution->add($entryDistribution);
			$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
			$this->assertNotNull($resultEntryDistribution->id);
			
			KalturaLog::debug("Returns Entry Distribution ID [$resultEntryDistribution->id]");
			return $resultEntryDistribution->id;
		}
		catch (KalturaException $e)
		{
			KalturaLog::err("Add EntryDistribution Exception code [" . $e->getCode() . "] message [" . $e->getMessage() . "]");
			if($e->getCode() != 'ENTRY_DISTRIBUTION_ALREADY_EXISTS')
				throw $e;
		}
		
		$entryDistributionFilter = new KalturaEntryDistributionFilter();
		$entryDistributionFilter->entryIdIn = $entryDistribution->entryId;
		$entryDistributionFilter->distributionProfileIdEqual = $entryDistribution->distributionProfileId;
		
		$entryDistributionList = $this->client->entryDistribution->listAction($entryDistributionFilter);
		
		$this->assertType('KalturaEntryDistributionListResponse', $entryDistributionList);
		$this->assertNotEquals($entryDistributionList->totalCount, 0);
		$this->assertEquals($entryDistributionList->totalCount, count($entryDistributionList->objects));
		
		$resultEntryDistribution = reset($entryDistributionList->objects);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		
		KalturaLog::debug("Returns Entry Distribution ID [$resultEntryDistribution->id]");
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->getAction()
	 * @param int $id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		KalturaLog::debug("testGet [" . print_r($id, true) . "]");
		$resultEntryDistribution = $this->client->entryDistribution->get($id);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->validateAction()
	 * @param int $id
	 * @return int
	 * @depends testGet
	 */
	public function testValidate($id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->validate($id);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;	
	}
	
	/**
	 * Tests EntryDistributionService->updateAction()
	 * @param KalturaEntryDistribution $entryDistribution
	 * @param int $id - returned from testValidate
	 * @return int
	 * @depends testValidate
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaEntryDistribution $entryDistribution, $id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->update($id, $entryDistribution);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->listAction()
	 * @param KalturaEntryDistributionFilter $entryDistributionFilter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaEntryDistributionFilter $entryDistributionFilter, KalturaFilterPager $pager = null)
	{
		$entryDistributionList = $this->client->entryDistribution->listAction($entryDistributionFilter, $pager);
		$this->assertType('KalturaEntryDistributionListResponse', $entryDistributionList);
		$this->assertNotEquals($entryDistributionList->totalCount, 0);
		$this->assertEquals($entryDistributionList->totalCount, count($entryDistributionList->objects));
	}
	
	/**
	 * Tests EntryDistributionService->submitAddAction()
	 * @param int $id
	 * @param bool $submitWhenReady
	 * @return int
	 * @depends testUpdate with data set #0
	 * @dataProvider provideData
	 */
	public function testSubmitAdd($submitWhenReady, $id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->submitAdd($id, $submitWhenReady);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->submitUpdateAction()
	 * @param int $id
	 * @return int
	 * @depends testSubmitAdd with data set #0
	 * @expectedException KalturaException
	 */
	public function testSubmitUpdate($id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->submitUpdate($id);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->submitFetchReportAction()
	 * @param int $id
	 * @return int
	 * @depends testSubmitAdd with data set #0
	 * @expectedException KalturaException
	 * 
	 */
	public function testSubmitFetchReport($id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->submitFetchReport($id);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->submitDeleteAction()
	 * @param int $id
	 * @return int
	 * @depends testSubmitAdd with data set #0
	 * @expectedException KalturaException
	 */
	public function testSubmitDelete($id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->submitDelete($id);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->retrySubmitAction()
	 * @param int $id
	 * @return int
	 * @depends testSubmitAdd with data set #0
	 */
	public function testRetrySubmit($id)
	{
		$resultEntryDistribution = $this->client->entryDistribution->retrySubmit($id);
		$this->assertType('KalturaEntryDistribution', $resultEntryDistribution);
		$this->assertNotNull($resultEntryDistribution->id);
		return $resultEntryDistribution->id;
	}
	
	/**
	 * Tests EntryDistributionService->deleteAction()
	 * @param int $id
	 * @return int
	 * @depends testRetrySubmit
	 */
	public function testDelete($id)
	{
		$this->client->entryDistribution->delete($id);
	}
}
