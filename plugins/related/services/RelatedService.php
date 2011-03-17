<?php
/**
 * Exposes specific functionality to retreive related entries
 * 
 * @service related
 * @package plugins.related
 * @subpackage api.services
 */
class RelatedService extends KalturaBaseService
{
	/**
	 * Return a list of related videos for a specific video entry
	 * 
	 * @action listRelatedMedieEntries
	 * @param string $entryId
	 * @param KalturaRelatedScope $scope
	 * @param int $pageSize
	 * @return KalturaRelatedResponse
	 */
	public function listRelatedMedieEntriesAction($entryId, KalturaRelatedScope $scope = null, $pageSize = 30)
	{
		$mediaService = new MediaService();
		$mediaService->initService('media', 'media', 'get');
		$entry = $mediaService->getAction($entryId);
		$pager = new KalturaFilterPager();

		if (!$scope)
			$scope = new KalturaRelatedScope();
			
		if ($pageSize > $pager->maxPageSize)
			throw new KalturaAPIException(KalturaRelatedErrors::MAX_PAGE_SIZE_EXCEEDED);
			
		$pager->pageSize = $pageSize;
		$originalKs = kCurrentContext::$ks;
		$adminKs = $this->getAdminSessionForPartner($entry->partnerId);
		$mediaEntryFilter = new KalturaMediaEntryFilter();
		$mediaEntryFilter->idNotIn = $entryId;
		$mediaEntryFilter->mediaTypeEqual = KalturaMediaType::VIDEO;
		$mediaEntryFilter->startDateLessThanOrEqualOrNull = time();
		$mediaEntryFilter->startDateGreaterThanOrEqualOrNull = time();
		kCurrentContext::initKsPartnerUser($adminKs);
		$mediaService->initService('media', 'media', 'list');
		
		$mediaEntryFilter->freeText = $entry->tags . ', ' . $entry->name;
		$mediaResponse = $mediaService->listAction($mediaEntryFilter, $pager);

		// if we have less results than the page size, get all entries
		if ($pager->pageSize > count($mediaResponse->objects))
		{
			$mediaEntryFilter->freeText = null;
			$allMediaResponse = $mediaService->listAction($mediaEntryFilter, $pager);
			
			// let's merge entries from all media response, keeping uniqueness
			$entries = array();
			foreach($mediaResponse->objects as $entry)
			{
				$entries[$entry->id] = $entry;
			}
			
			foreach($allMediaResponse->objects as $entry)
			{
				$entries[$entry->id] = $entry;
				if (count($entries) >= $pager->pageSize)
					break;
			}
			$mediaResponse->objects = new KalturaMediaEntryArray();
			foreach($entries as $entry)
			{
				$mediaResponse->objects[] = $entry;
			}
		}
		
		$entryIds = array();
		foreach($mediaResponse->objects as $entry)
		{
			$entryIds[] = $entry->id;
		}
		
		$metadataService = new MetadataService();
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
		$metadataFilter->objectIdIn = implode(',', $entryIds);
		$metadataPager = new KalturaFilterPager();
		$metadataPager->pageSize =  $pager->pageSize;
		$metadataService->initService('metadata_metadata', 'metadata_metadata', 'list');
		$metadataResponse = $metadataService->listAction($metadataFilter, $metadataPager);
		
		kCurrentContext::initKsPartnerUser($originalKs);
		
		$relatedResponse = new KalturaRelatedResponse();
		$relatedResponse->entryObjects = $mediaResponse->objects;
		$relatedResponse->metadataObjects = $metadataResponse->objects;
		return $relatedResponse;
	}
	
	protected function getAdminSessionForPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			throw new Exception('Partner not found when trying to start session');
		
		$sessionService = new SessionService();
		$sessionService->initService('session', 'session', 'start');
		return $sessionService->startAction($partner->getAdminSecret(), "", KalturaSessionType::ADMIN, $partnerId);
	}
}
