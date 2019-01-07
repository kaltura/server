<?php
/**
 * Created by PhpStorm.
 * User: hila.karimov
 * Date: 12/24/18
 * Time: 10:38 PM
 */

class BulkUploadMediaEntryEngineFilter extends BulkUploadEngineFilter
{
	const OBJECT_TYPE_TITLE = 'media entry';
	
	const ENTRY_TAG_XPATH = '/mrss/channel/item/entryId';
	
	const ACTION_TAG_XPATH = '/mrss/channel/item/action';
	
	protected function listObjects(KalturaFilter $filter, KalturaFilterPager $pager = null)
	{
		KBatchBase::impersonate($this->currentPartnerId);
		
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
		$entryId = $bulkUploadResult->objectId;
		
		$xml = file_get_contents($this->data->filePath);
		$doc = new KDOMDocument();
		$doc->loadXML($xml);
		
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
		$bulkUploadJobData->fileName = $this->job->id . '_' . $bulkUploadResult->objectId . '.xml';
		
		KBatchBase::$kClient->media->bulkUploadAdd($tmpFilePath, $bulkUploadJobData);
	}
	
	protected function deleteObjectFromResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		// TODO: Implement deleteObjectFromResult() method.
	}
	
	protected function fillUploadResultInstance($object)
	{
		$bulkUploadResult = new KalturaBulkUploadResultEntry();
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		$bulkUploadResult->objectId = $object->id;
		
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
		return KalturaBulkUploadObjectType::ENTRY;
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