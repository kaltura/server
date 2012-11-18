<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');

/**
 * externalMedia service test case.
 */
class ExternalMediaServiceTest extends ExternalMediaServiceTestBase
{
	/**
	 * Tests externalMedia->count action
	 * @param KalturaExternalMediaEntry $entry 
	 * @param KalturaExternalMediaEntryFilter $filter External media entry filter
	 * @dataProvider provideData
	 */
	public function testCount(KalturaExternalMediaEntry $entry, KalturaExternalMediaEntryFilter $filter = null)
	{
		$externalMediaPlugin = KalturaExternalMediaClientPlugin::get($this->client);
		$externalMediaPlugin->externalMedia->add($entry);
		$resultObject = $externalMediaPlugin->externalMedia->count($filter);
		
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('int', $resultObject);
		else
			$this->assertType('int', $resultObject);
			
		$this->assertGreaterThan(0, $resultObject);
	}

	/**
	 * Tests externalMedia->listAction action
	 * @param KalturaExternalMediaEntryFilter $filter External media entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaExternalMediaEntryListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaExternalMediaEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaExternalMediaEntryListResponse $reference)
	{
		$resultObject = $this->client->externalMedia->listAction($filter, $pager);
		/* @var $resultObject KalturaExternalMediaEntryListResponse */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntryListResponse', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntryListResponse', $resultObject);
	
		foreach($resultObject->objects as $entry)
		{
			/* @var $entry KalturaExternalMediaEntry */
			if(method_exists($this, 'assertInstanceOf'))
				$this->assertInstanceOf('KalturaExternalMediaEntry', $entry, "Entry id [$entry->id]");
			else
				$this->assertType('KalturaExternalMediaEntry', $entry, "Entry id [$entry->id]");
				
			if($filter->externalSourceTypeEqual)
				$this->assertEquals($filter->externalSourceTypeEqual, $entry->externalSourceType, "Entry id [$entry->id]");
			else
				$this->assertNotNull($entry->externalSourceType, "Entry id [$entry->id]");
		}
	}
}

