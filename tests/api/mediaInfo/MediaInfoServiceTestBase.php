<?php

/**
 * mediaInfo service base test case.
 */
abstract class MediaInfoServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests mediaInfo->listAction action
	 * @param KalturaMediaInfoFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaMediaInfoListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference)
	{
		$resultObject = $this->client->mediaInfo->listAction($filter, $pager);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMediaInfoListResponse', $resultObject);
		else
			$this->assertType('KalturaMediaInfoListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($resultObject);
	}

	/**
	 * Validates testListAction results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaMediaInfoListResponse $resultObject
	 */
	protected function validateListAction(KalturaMediaInfoListResponse $resultObject){}

}
