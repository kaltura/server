<?php
/**
 * This engine supports deletion of user entries based on the input filter
 *
 * @package plugins.bulkUploadFilter
 * @subpackage batch
 */
class BulkUploadUserEntryEngineFilter extends BulkUploadEngineFilter
{
	const OBJECT_TYPE_TITLE = 'user entry';

	/* get a list of objects according to the input filter
	 *
	 * @see BulkUploadEngineFilter::listObjects()
	 */
	protected function listObjects(KalturaFilter $filter, KalturaFilterPager $pager = null)
	{
		if($filter instanceof KalturaUserEntryFilter)
		{
			return KBatchBase::$kClient->userEntry->listAction($filter, $pager);
		}

		else
		{
			throw new KalturaBatchException("Unsupported filter: {get_class($filter)}", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
	}

	protected function createObjectFromResultAndJobData (KalturaBulkUploadResult $bulkUploadResult)
	{

	}

	protected function deleteObjectFromResult (KalturaBulkUploadResult $bulkUploadResult)
	{
		return KBatchBase::$kClient->userEntry->delete($bulkUploadResult->userEntryId);
	}

	/**
	 * create specific instance of BulkUploadResult and set it's properties
	 * @param $object - Result is being created from KalturaUserEntry
	 *
	 * @see BulkUploadEngineFilter::fillUploadResultInstance()
	 */
	protected function fillUploadResultInstance ($object)
	{
		$bulkUploadResult = new KalturaBulkUploadResultUserEntry();
		if($object instanceof KalturaUserEntry)
		{
			//get user entry object based on the entry details
			$filter = new KalturaUserEntryFilter();
			$filter->idEqual = $object->id;
			$filter->userIdEqual = $object->userId;
			$filter->partnerId = $object->partnerId;
			$list = $this->listObjects($filter);
			if(count($list->objects))
			{
				$userEntry = reset($list->objects);
			}
		}
		if($userEntry)
		{
			$bulkUploadResult->objectId = $userEntry->id.':'.$userEntry->userId;
			$bulkUploadResult->objectStatus = $userEntry->status;
			$bulkUploadResult->userEntryId = $userEntry->id;
			$bulkUploadResult->action = KalturaBulkUploadAction::DELETE;
		}
		return $bulkUploadResult;
	}

	protected function getBulkUploadResultObjectType()
	{
		return KalturaBulkUploadObjectType::USER_ENTRY;
	}

	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}

}