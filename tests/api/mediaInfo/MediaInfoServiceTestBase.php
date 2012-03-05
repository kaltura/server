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
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMediaInfoListResponse', $resultObject);
		else
			$this->assertNotType('KalturaMediaInfoListResponse', get_class($resultObject));
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	abstract protected function validateListAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference);
}
