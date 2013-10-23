<?php

/**
 * Search caption asset items
 *
 * @service captionAssetItem
 * @package plugins.captionSearch
 * @subpackage api.services
 */
class CaptionAssetItemService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		$ks = kCurrentContext::$ks_object ? kCurrentContext::$ks_object : null;
		
		if (($actionName == 'search') &&
		  (!$ks || (!$ks->isAdmin() && !$ks->verifyPrivileges(ks::PRIVILEGE_LIST, ks::PRIVILEGE_WILDCARD))))
		{
			KalturaCriterion::enableTag(KalturaCriterion::TAG_WIDGET_SESSION);
			entryPeer::setUserContentOnly(true);
		}
		
		parent::initService($serviceId, $serviceName, $actionName);
		
		if($actionName != 'parse')
		{
			$this->applyPartnerFilterForClass('asset');
			$this->applyPartnerFilterForClass('CaptionAssetItem');
		}
		
		if(!CaptionSearchPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
	
    /**
     * Parse content of caption asset and index it
     *
     * @action parse
     * @param string $captionAssetId
     * @throws KalturaCaptionErrors::CAPTION_ASSET_ID_NOT_FOUND
     */
    function parseAction($captionAssetId)
    {
		$captionAsset = assetPeer::retrieveById($captionAssetId);
		if(!$captionAsset)
			throw new KalturaAPIException(KalturaCaptionErrors::CAPTION_ASSET_ID_NOT_FOUND, $captionAssetId);
		
		$captionAssetItems = CaptionAssetItemPeer::retrieveByAssetId($captionAssetId);
		foreach($captionAssetItems as $captionAssetItem)
		{
			/* @var $captionAssetItem CaptionAssetItem */
			$captionAssetItem->delete();
		}
		
		// make sure that all old items are deleted from the sphinx before creating the new ones
		kEventsManager::flushEvents();
		
		$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$content = kFileSyncUtils::file_get_contents($syncKey, true, false);
		if(!$content)
			return;
			
    	$captionsContentManager = kCaptionsContentManager::getCoreContentManager($captionAsset->getContainerFormat());
    	if(!$captionsContentManager)
    		return;
    		
    	$itemsData = $captionsContentManager->parse($content);
    	foreach($itemsData as $itemData)
    	{
    		$item = new CaptionAssetItem();
    		$item->setCaptionAssetId($captionAsset->getId());
    		$item->setEntryId($captionAsset->getEntryId());
    		$item->setPartnerId($captionAsset->getPartnerId());
    		$item->setStartTime($itemData['startTime']);
    		$item->setEndTime($itemData['endTime']);
    		$content = '';
    		foreach ($itemData['content'] as $curChunk)
    			$content .= $curChunk['text'];
    			
    		//Make sure there are no invalid chars in the caption asset items to avoid braking the search request by providing invalid XML
    		$content = kString::stripUtf8InvalidChars($content);
    		$content = kXml::stripXMLInvalidChars($content);
    		
    		$item->setContent($content);
    		$item->save();
    	}
    }
	
	/**
	 * Search caption asset items by filter, pager and free text
	 *
	 * @action search
	 * @param KalturaBaseEntryFilter $entryFilter
	 * @param KalturaCaptionAssetItemFilter $captionAssetItemFilter
	 * @param KalturaFilterPager $captionAssetItemPager
	 * @return KalturaCaptionAssetItemListResponse
	 */
	function searchAction(KalturaBaseEntryFilter $entryFilter = null, KalturaCaptionAssetItemFilter $captionAssetItemFilter = null, KalturaFilterPager $captionAssetItemPager = null)
	{
		if (!$captionAssetItemPager)
			$captionAssetItemPager = new KalturaFilterPager();
			
		if (!$captionAssetItemFilter)
			$captionAssetItemFilter = new KalturaCaptionAssetItemFilter();

		$captionAssetItemFilter->validatePropertyNotNull(array("contentLike", "contentMultiLikeOr", "contentMultiLikeAnd"));
		
		$captionAssetItemCoreFilter = new CaptionAssetItemFilter();
		$captionAssetItemFilter->toObject($captionAssetItemCoreFilter);
		
		if($entryFilter || kEntitlementUtils::getEntitlementEnforcement())
		{
			$entryCoreFilter = new entryFilter();
			if($entryFilter)
				$entryFilter->toObject($entryCoreFilter);
			$entryCoreFilter->setPartnerSearchScope($this->getPartnerId());
			$this->addEntryAdvancedSearchFilter($captionAssetItemFilter, $entryCoreFilter);
				
			$entryCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
			$entryCoreFilter->attachToCriteria($entryCriteria);
			$entryCriteria->applyFilters();
				
			$entryIds = $entryCriteria->getFetchedIds();
			if(!$entryIds || !count($entryIds))
				$entryIds = array('NOT_EXIST');
				
			$captionAssetItemCoreFilter->setEntryIdIn($entryIds);
		}
		$captionAssetItemCriteria = KalturaCriteria::create(CaptionAssetItemPeer::OM_CLASS);
		
		$captionAssetItemCoreFilter->attachToCriteria($captionAssetItemCriteria);
		$captionAssetItemPager->attachToCriteria($captionAssetItemCriteria);
		
		$dbList = CaptionAssetItemPeer::doSelect($captionAssetItemCriteria);
		
		$list = KalturaCaptionAssetItemArray::fromDbArray($dbList);
		$response = new KalturaCaptionAssetItemListResponse();
		$response->objects = $list;
		$response->totalCount = $captionAssetItemCriteria->getRecordsCount();
		return $response;
	}
	
	private function addEntryAdvancedSearchFilter(KalturaCaptionAssetItemFilter $captionAssetItemFilter, entryFilter $entryCoreFilter)
	{
		//create advanced filter on entry caption
		$entryCaptionAdvancedSearch = new EntryCaptionAssetSearchFilter();
		$entryCaptionAdvancedSearch->setContentLike($captionAssetItemFilter->contentLike);
		$entryCaptionAdvancedSearch->setContentMultiLikeAnd($captionAssetItemFilter->contentMultiLikeAnd);
		$entryCaptionAdvancedSearch->setContentMultiLikeOr($captionAssetItemFilter->contentMultiLikeOr);
		$inputAdvancedSearch = $entryCoreFilter->getAdvancedSearch();
		if(!is_null($inputAdvancedSearch))
		{
			$advancedSearchOp = new AdvancedSearchFilterOperator();
			$advancedSearchOp->setType(AdvancedSearchFilterOperator::SEARCH_AND);
			$advancedSearchOp->setItems(array ($inputAdvancedSearch, $entryCaptionAdvancedSearch));
			$entryCoreFilter->setAdvancedSearch($advancedSearchOp);
		}
		else
		{
			$entryCoreFilter->setAdvancedSearch($entryCaptionAdvancedSearch);
		}
	}
	
	
	/**
	 * Search caption asset items by filter, pager and free text
	 *
	 * @action searchEntries
	 * @param KalturaBaseEntryFilter $entryFilter
	 * @param KalturaCaptionAssetItemFilter $captionAssetItemFilter
	 * @param KalturaFilterPager $captionAssetItemPager
	 * @return KalturaBaseEntryListResponse
	 */
	public function searchEntriesAction (KalturaBaseEntryFilter $entryFilter = null, KalturaCaptionAssetItemFilter $captionAssetItemFilter = null, KalturaFilterPager $captionAssetItemPager = null)
	{
		if (!$captionAssetItemPager)
			$captionAssetItemPager = new KalturaFilterPager();
			
		if (!$captionAssetItemFilter)
			$captionAssetItemFilter = new KalturaCaptionAssetItemFilter();

		$captionAssetItemFilter->validatePropertyNotNull(array("contentLike", "contentMultiLikeOr", "contentMultiLikeAnd"));
		
		$captionAssetItemCoreFilter = new CaptionAssetItemFilter();
		$captionAssetItemFilter->toObject($captionAssetItemCoreFilter);
		
		if($entryFilter || kEntitlementUtils::getEntitlementEnforcement())
		{
			$entryCoreFilter = new entryFilter();
			if($entryFilter)
				$entryFilter->toObject($entryCoreFilter);
			$entryCoreFilter->setPartnerSearchScope($this->getPartnerId());
			$this->addEntryAdvancedSearchFilter($captionAssetItemFilter, $entryCoreFilter);
				
			$entryCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
			$entryCoreFilter->attachToCriteria($entryCriteria);
			$entryCriteria->applyFilters();
				
			$entryIds = $entryCriteria->getFetchedIds();
			if(!$entryIds || !count($entryIds))
				$entryIds = array('NOT_EXIST');
				
			$captionAssetItemCoreFilter->setEntryIdIn($entryIds);
		}
		$captionAssetItemCriteria = KalturaCriteria::create(CaptionAssetItemPeer::OM_CLASS);
		
		$captionAssetItemCoreFilter->attachToCriteria($captionAssetItemCriteria);
		$captionAssetItemPager->attachToCriteria($captionAssetItemCriteria);
		$captionAssetItemCriteria->setGroupByColumn('str_entry_id');
		$captionAssetItemCriteria->setSelectColumn('str_entry_id');
		$captionAssetItemCriteria->applyFilters();
		
		$entryIds = $captionAssetItemCriteria->getFetchedIds();
		$dbList = entryPeer::retrieveByPKs($entryIds);
		$list = KalturaBaseEntryArray::fromEntryArray($dbList);
		$response = new KalturaBaseEntryListResponse();
		$response->objects = $list;
		$response->totalCount = $captionAssetItemCriteria->getRecordsCount();
		
		return $response;
	}
}
