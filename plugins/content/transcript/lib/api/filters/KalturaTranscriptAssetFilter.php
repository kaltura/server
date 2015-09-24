<?php
/**
 * @package plugins.transcript
 * @subpackage api.filters
 */
class KalturaTranscriptAssetFilter extends KalturaTranscriptAssetBaseFilter
{	
	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);

		$response = new KalturaTranscriptAssetListResponse();
		$response->objects = KalturaTranscriptAssetArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
