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
		parent::initService($serviceId, $serviceName, $actionName);
		
		parent::applyPartnerFilterForClass(new assetPeer());
		
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
		$captionAsset = assetPeer::retrieveByIdNoFilter($captionAssetId);
		if(!$captionAsset)
			throw new KalturaAPIException(KalturaCaptionErrors::CAPTION_ASSET_ID_NOT_FOUND, $captionAssetId);
		
		$captionAssetItems = CaptionAssetItemPeer::retrieveByAssetId($captionAssetId);
		foreach($captionAssetItems as $captionAssetItem)
		{
			/* @var $captionAssetItem CaptionAssetItem */
			$captionAssetItem->delete();
		}
		
		$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$content = kFileSyncUtils::file_get_contents($syncKey, true, false);
		if(!$content)
			return;
			
    	$captionsContentManager = kCaptionsContentManager::getCoreContentManager($captionAsset->getContainerFormat());
    	$itemsData = $captionsContentManager->parse($content);
    	foreach($itemsData as $itemData)
    	{
    		$item = new CaptionAssetItem();
    		$item->setCaptionAssetId($captionAsset->getId());
    		$item->setStartTime($itemData['startTime']);
    		$item->setEndTime($itemData['endTime']);
    		$item->setContent($itemData['content']);
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
		if (!$entryFilter)
			$entryFilter = new KalturaBaseEntryFilter();

		if (!$captionAssetItemPager)
			$captionAssetItemPager = new KalturaFilterPager();
			
		if (!$captionAssetItemFilter)
			$captionAssetItemFilter = new KalturaCaptionAssetItemFilter();
			
		$entryCoreFilter = new entryFilter();
		$entryFilter->toObject($entryCoreFilter);

		$entryCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryCoreFilter->attachToCriteria($entryCriteria);
		$entryCriteria->applyFilters();
		
		$entryIds = $entryCriteria->getFetchedIds();
		
		$captionAssetItemCriteria = KalturaCriteria::create(CaptionAssetItemPeer::OM_CLASS);
		
		$captionAssetItemCoreFilter = new CaptionAssetItemFilter();
		$captionAssetItemFilter->toObject($captionAssetItemCoreFilter);
		$captionAssetItemCoreFilter->setEntryIdIn($entryIds);
		
		$captionAssetItemCoreFilter->attachToCriteria($captionAssetItemCriteria);
		$captionAssetItemPager->attachToCriteria($captionAssetItemCriteria);
		
		$dbList = CaptionAssetItemPeer::doSelect($captionAssetItemCriteria);
		
		$list = KalturaCaptionAssetArray::fromDbArray($dbList);
		$response = new KalturaCaptionAssetListResponse();
		$response->objects = $list;
		$response->totalCount = $captionAssetItemCriteria->getRecordsCount();
		return $response;    
	}
}
