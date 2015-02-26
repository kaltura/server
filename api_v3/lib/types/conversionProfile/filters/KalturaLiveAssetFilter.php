<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaLiveAssetFilter extends KalturaLiveAssetBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, assetType::LIVE);
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
