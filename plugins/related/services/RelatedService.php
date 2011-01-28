<?php
/**
 * Exposes specific functionality to retreive related entries
 * 
 * @service related
 * @author Roman
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
		$mediaService->initService('media', 'get');
		$entry = $mediaService->getAction($entryId);

		if (!$scope)
			$scope = new KalturaRelatedScope();
			
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$originalKs = kCurrentContext::$ks;
		$adminKs = $this->getAdminSessionForPartner($entry->partnerId);
		
		$mediaEntryFilter = new KalturaMediaEntryFilter();
		$mediaEntryFilter->tagsMultiLikeOr = $entry->tags;
		$mediaEntryFilter->orderBy = KalturaMediaEntryOrderBy::CREATED_AT_DESC;
		$mediaEntryFilter->advancedSearch = $this->getAdvancedSearch($entry, $scope);
		
		kCurrentContext::initKsPartnerUser($adminKs);
		$mediaService->initService('media', 'list');
		$response = $mediaService->listAction($mediaEntryFilter, $pager);
		kCurrentContext::initKsPartnerUser($originalKs);

		return $response;
	}
	
	protected function getAdminSessionForPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			throw new Exception('Partner not found when trying to start session');
		
		$sessionService = new SessionService();
		$sessionService->initService('session', 'start');
		return $sessionService->startAction($partner->getAdminSecret(), "", KalturaSessionType::ADMIN, $partnerId);
	}
	
	protected function getAdvancedSearch()
	{
		return null;
		$medataProfile = null;
		$metadataProfile = new MetadataProfileService();
		$metadataProfilesResponse = $metadataProfile->listAction();
		if (count($metadataProfilesResponse->objects) > 0)
			$medataProfile = $metadataProfilesResponse->objects[0];
			
		$advancedSearch = new KalturaSearchOperator();
		$advancedSearch->items = array();
		$filter1 = new KalturaSearchComparableCondition();
		$filter1->comparison = KalturaSearchConditionComparison::EQUEL;
		$filter1->field = 'tags';
		$filter1->field = 'logo';
		
	}
}
