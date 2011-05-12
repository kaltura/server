<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/EntryDistributionServiceBaseTest.php');

/**
 * entryDistribution service test case.
 */
class EntryDistributionServiceTest extends EntryDistributionServiceBaseTest
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
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaEntryDistribution $entryDistribution, KalturaEntryDistribution $reference)
	{
		parent::validateAdd($entryDistribution, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaEntryDistribution $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests entryDistribution->validate action
	 * @param KalturaEntryDistribution $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testValidate(KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->validate($id, $reference);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaEntryDistribution $entryDistribution, KalturaEntryDistribution $reference, $id)
	{
		parent::validateUpdate($entryDistribution, $reference);
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
	 * Validates testList results
	 */
	protected function validateList(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null, KalturaEntryDistributionListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests entryDistribution->submitAdd action
	 * @param bool $submitWhenReady
	 * @param KalturaEntryDistribution $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testSubmitAdd($submitWhenReady = null, KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->submitAdd($id, $submitWhenReady, $reference);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests entryDistribution->submitUpdate action
	 * @param KalturaEntryDistribution $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testSubmitUpdate(KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->submitUpdate($id, $reference);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests entryDistribution->submitFetchReport action
	 * @param KalturaEntryDistribution $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testSubmitFetchReport(KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->submitFetchReport($id, $reference);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests entryDistribution->submitDelete action
	 * @param KalturaEntryDistribution $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testSubmitDelete(KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->submitDelete($id, $reference);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests entryDistribution->retrySubmit action
	 * @param KalturaEntryDistribution $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testRetrySubmit(KalturaEntryDistribution $reference, $id)
	{
		$resultObject = $this->client->entryDistribution->retrySubmit($id, $reference);
		$this->assertType('KalturaEntryDistribution', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
