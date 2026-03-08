<?php
/**
 * @package plugins.markdown
 * @subpackage api.filters
 */
class KalturaMarkdownAssetFilter extends KalturaMarkdownAssetBaseFilter
{	
	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, MarkdownPlugin::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN));
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);

		$response = new KalturaMarkdownAssetListResponse();
		$response->objects = KalturaMarkdownAssetArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, MarkdownPlugin::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN));
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
