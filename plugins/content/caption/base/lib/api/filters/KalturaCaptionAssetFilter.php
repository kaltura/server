<?php
/**
 * @package plugins.caption
 * @subpackage api.filters
 */
class KalturaCaptionAssetFilter extends KalturaCaptionAssetBaseFilter
{

	static private $map_between_objects = array
	(
		"captionParamsIdEqual" => "_eq_flavor_params_id",
		"captionParamsIdIn" => "_in_flavor_params_id",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}	

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		$this->validateEntryIdsFiltered();
		$entryIds = $this->retrieveEntryIdsFiltered();
		$entryIds = kParentChildEntryUtils::getParentEntryIds($entryIds);
		$this->entryIdEqual = null;
		$this->entryIdIn =  implode(',', $entryIds);

		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaCaptionAssetListResponse();
		$response->objects = KalturaCaptionAssetArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
