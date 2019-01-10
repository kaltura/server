<?php

class BulkUploadMediaEntryEngineFilter extends BulkUploadEngineFilter
{
	const OBJECT_TYPE_TITLE = 'media entry';
	
	const ENTRY_TAG_XPATH = '/mrss/channel/item/entryId';
	
	const ACTION_TAG_XPATH = '/mrss/channel/item/action';
	
	protected function listObjects(KalturaFilter $filter, KalturaFilterPager $pager = null)
	{
		$filter->orderBy = "+createdAt";
		if ($filter instanceof KalturaBaseEntryFilter)
		{
			return KBatchBase::$kClient->baseEntry->listAction($filter, $pager);
		}
		else
		{
			throw new KalturaBatchException('Unsupported filter: {' . get_class($filter) . '}', KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		
		KBatchBase::unimpersonate();
	}
	
	protected function createObjectFromResultAndJobData(KalturaBulkUploadResult $bulkUploadResult)
	{
		$entryId = $bulkUploadResult->jobObjectId;
		
		$doc = new KDOMDocument();
		$doc->load($this->data->filePath);
		
		$xpath = new DOMXPath($doc);
		
		$items = $xpath->query(self::ENTRY_TAG_XPATH);
		if (!$items->length)
		{
			throw new KalturaBatchException ('No {entryId} tag found in template bulk upload XML provided!');
		}
		
		foreach ($items as $item)
		{
			/* @var $item DOMNode */
			$item->nodeValue = $entryId;
		}
		
		$tmpFilePath = kFile::createTempFile($doc->saveXML());
		
		$bulkUploadJobData = new KalturaBulkUploadXmlJobData();
		$bulkUploadJobData->fileName = $this->job->id . '_' . $entryId . '.xml';
		
		KBatchBase::$kClient->media->bulkUploadAdd($tmpFilePath, $bulkUploadJobData);
	}
	
	protected function deleteObjectFromResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		// TODO: Implement deleteObjectFromResult() method.
	}
	
	protected function fillUploadResultInstance($object)
	{
		$bulkUploadResult = new KalturaBulkUploadResultJob();
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->jobObjectId = $object->id;
		
		$doc = new KDOMDocument();
		$doc->load($this->data->filePath);
		
		$xpath = new DOMXPath($doc);
		
		$actions = $xpath->query(self::ACTION_TAG_XPATH);
		if (!$actions->length)
		{
			throw new KalturaBatchException ('No {action} tag found in template bulk upload XML provided!');
		}
		
		foreach ($actions as $action)
		{
			/* @var $action DOMNode */
			if (strval($action->nodeValue) == 'add')
			{
				throw new KalturaBatchException ('{action} tag value can only be set to values [update] and [delete]');
			}
		}
		
		return $bulkUploadResult;
	}
	
	protected function getBulkUploadResultObjectType()
	{
		return KalturaBulkUploadObjectType::JOB;
	}
	
	/**
	 *
	 * Get object type title for messaging purposes
	 */
	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}
}