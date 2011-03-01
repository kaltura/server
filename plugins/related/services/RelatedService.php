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
	 * @param KalturaFilterPager $pager
	 * @return KalturaMediaListResponse
	 */
	public function listRelatedMedieEntriesAction($entryId, KalturaRelatedScope $scope = null, KalturaFilterPager $pager = null)
	{
		$mediaService = new MediaService();
		$mediaService->initService('media', 'media', 'get');
		$entry = $mediaService->getAction($entryId);

		if (!$scope)
			$scope = new KalturaRelatedScope();
			
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		if ($pager->pageIndex != 1)
			throw new KalturaAPIException(KalturaRelatedErrors::PAGING_NOT_SUPPORTED);
			 
		$originalKs = kCurrentContext::$ks;
		$adminKs = $this->getAdminSessionForPartner($entry->partnerId);
		$mediaEntryFilter = new KalturaMediaEntryFilter();
		$mediaEntryFilter->idNotIn = $entryId;
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
			$mediaResponse->totalCount = count($entries);
		}
		
		kCurrentContext::initKsPartnerUser($originalKs);
		
		return $mediaResponse;
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
