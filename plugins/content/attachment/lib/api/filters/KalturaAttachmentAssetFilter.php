<?php
/**
 * @package plugins.attachment
 * @subpackage api.filters
 */
class KalturaAttachmentAssetFilter extends KalturaAttachmentAssetBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaAttachmentAssetListResponse();
		$response->objects = KalturaAttachmentAssetArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
