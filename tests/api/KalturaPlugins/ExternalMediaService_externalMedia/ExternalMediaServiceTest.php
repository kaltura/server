<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');

/**
 * externalMedia service test case.
 */
class ExternalMediaServiceTest extends ExternalMediaServiceTestBase
{
	/**
	 * Tests baseEntry->add action
	 * @param KalturaExternalMediaEntry $entry 
	 * @param KalturaExternalMediaEntry $reference
	 * @return string
	 * @dataProvider provideData
	 */
	public function testBaseEntryAdd(KalturaBaseEntry $entry, KalturaExternalMediaEntry $reference)
	{
		$entry->referenceId = uniqid('ref');
		$reference->referenceId = $entry->referenceId;
		
		$resultObject = $this->client->baseEntry->add($entry, KalturaEntryType::EXTERNAL_MEDIA);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf(get_class($entry), $resultObject);
		else
			$this->assertType(get_class($entry), $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->assertEquals($resultObject->status, KalturaEntryStatus::READY);
		
		return $resultObject;
	}
	
	/**
	 * Tests baseEntry->add action
	 * @param KalturaExternalMediaEntry $entry 
	 * @param string $code
	 * @dataProvider provideData
	 */
	public function testFailBaseEntryAdd(KalturaExternalMediaEntry $entry, $code)
	{
		try
		{
			$this->client->baseEntry->add($entry, KalturaEntryType::EXTERNAL_MEDIA);
		}
		catch(KalturaException $e)
		{
			$this->assertEquals($code, $e->getCode());
		}
	}

	/* (non-PHPdoc)
	 * @see ExternalMediaServiceTestBase::validateAdd()
	 */
	protected function validateAdd(KalturaExternalMediaEntry $resultObject)
	{
		$this->assertEquals($resultObject->status, KalturaEntryStatus::READY);
	}
	
	/**
	 * Tests baseEntry->update action
	 * @param KalturaExternalMediaEntry $entry 
	 * @param KalturaExternalMediaEntry $updateEntry
	 * @param KalturaExternalMediaEntry $reference
	 * @depends testBaseEntryAdd with data set #1
	 * @dataProvider provideData
	 * @return KalturaExternalMediaEntry
	 */
	public function testBaseEntryUpdate(KalturaExternalMediaEntry $entry, KalturaExternalMediaEntry $updateEntry, KalturaExternalMediaEntry $reference)
	{
		sleep(2);
		$resultObject = $this->client->baseEntry->update($entry->id, $updateEntry);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntry', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntry', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->assertNotEquals($resultObject->updatedAt, $entry->updatedAt);
		$this->validateUpdate($resultObject);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->approve action
	 * @param KalturaExternalMediaEntry $entry 
	 * @depends testBaseEntryAdd with data set #2
	 * @dataProvider provideData
	 * @return KalturaExternalMediaEntry
	 */
	public function testBaseEntryApprove(KalturaExternalMediaEntry $entry)
	{
		$this->client->baseEntry->approve($entry->id);
		$resultObject = $this->client->baseEntry->get($entry->id);
		/* @var $resultObject KalturaExternalMediaEntry */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntry', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->assertEquals(KalturaEntryModerationStatus::APPROVED, $resultObject->moderationStatus);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->add action
	 * @param KalturaExternalMediaEntry $entry 
	 * @depends testBaseEntryUpdate with data set #0
	 * @dataProvider provideData
	 * @return KalturaExternalMediaEntry
	 */
	public function testBaseEntryReject(KalturaExternalMediaEntry $entry)
	{
		$this->client->baseEntry->reject($entry->id);
		$resultObject = $this->client->baseEntry->get($entry->id);
		/* @var $resultObject KalturaExternalMediaEntry */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaExternalMediaEntry', $resultObject);
		else
			$this->assertType('KalturaExternalMediaEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->assertEquals(KalturaEntryModerationStatus::REJECTED, $resultObject->moderationStatus);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->delete action
	 * @param KalturaExternalMediaEntry $entry 
	 * @depends testBaseEntryApprove with data set #0
	 */
	public function testBaseEntryDelete(KalturaExternalMediaEntry $entry)
	{
		$this->client->baseEntry->delete($entry->id);
		try
		{
			$resultObject = $this->client->baseEntry->get($entry->id);
			$this->fail("Entry no deleted");
		}
		catch(KalturaException $e)
		{
		}
	}
	
	/**
	 * Tests thumbAsset->add action
	 * @param KalturaExternalMediaEntry $entry
	 * @depends testBaseEntryAdd with data set #0
	 * @dataProvider provideData
	 * @return KalturaThumbAsset
	 */
	public function testThumbAssetAdd(KalturaExternalMediaEntry $entry)
	{
		$thumbAsset = new KalturaThumbAsset();
		$resultObject = $this->client->thumbAsset->add($entry->id, $thumbAsset);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		else
			$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->assertEquals($resultObject->entryId, $entry->id);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->setContent action
	 * @param KalturaThumbAsset $thumbAsset
	 * @param KalturaContentResource $contentResource
	 * @depends testThumbAssetAdd with data set #0
	 * @dataProvider provideData
	 * @return KalturaThumbAsset
	 */
	public function testThumbAssetSetContent(KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource)
	{
		$resultObject = $this->client->thumbAsset->setContent($thumbAsset->id, $contentResource);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		else
			$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->setContent action
	 * @param KalturaThumbAsset $thumbAsset
	 * @param KalturaContentResource $contentResource
	 * @depends testThumbAssetSetContent with data set #0
	 * @dataProvider provideData
	 * @return KalturaThumbAsset
	 */
	public function testThumbAssetSetContentAgain(KalturaThumbAsset $thumbAsset, KalturaContentResource $contentResource)
	{
		$resultObject = $this->client->thumbAsset->setContent($thumbAsset->id, $contentResource);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		else
			$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->setAsDefault action
	 * @param KalturaThumbAsset $thumbAsset
	 * @depends testThumbAssetSetContent with data set #0
	 * @dataProvider provideData
	 * @return KalturaThumbAsset
	 */
	public function testThumbAssetSetAsDefault(KalturaThumbAsset $thumbAsset)
	{
		$this->client->thumbAsset->setAsDefault($thumbAsset->id);
		$resultObject = $this->client->thumbAsset->get($thumbAsset->id);
		/* @var $resultObject KalturaThumbAsset */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaThumbAsset', $resultObject);
		else
			$this->assertType('KalturaThumbAsset', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->assertContains('default_thumb', $resultObject->tags);
		
		return $resultObject;
	}
	
	/**
	 * Tests thumbAsset->setAsDefault action
	 * @param KalturaThumbAsset $thumbAsset
	 * @depends testThumbAssetSetContent with data set #0
	 * @dataProvider provideData
	 * @return KalturaThumbAsset
	 */
	public function testThumbAssetList(KalturaThumbAsset $thumbAsset)
	{
		$filter = new KalturaThumbAssetFilter();
		$filter->entryIdEqual = $thumbAsset->entryId;
		$filter->tagsLike = 'default_thumb';
		
		$resultObject = $this->client->thumbAsset->listAction($filter);
		/* @var $resultObject KalturaThumbAssetListResponse */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaThumbAssetListResponse', $resultObject);
		else
			$this->assertType('KalturaThumbAssetListResponse', $resultObject);
		$this->assertEquals(1, $resultObject->totalCount);
		$this->assertEquals(1, count($resultObject->objects));
		
		$firstResultObject = reset($resultObject->objects);
		$this->assertEquals($thumbAsset->id, $firstResultObject->id);
		
		return $firstResultObject;
	}
	
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

