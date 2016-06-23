<?php
/**
 * @package plugins.attachment
 * @subpackage api.filters
 */
class KalturaAttachmentAssetFilter extends KalturaAttachmentAssetBaseFilter
{
	/**
	 * @dynamicType KalturaAttachmentType
	 * @var string
	 */
	public $typeIn;
	
	/**
	 * @dynamicType KalturaAttachmentType
	 * @var string
	 */
	public $typeNotIn;
	
	static private $map_between_objects = array
	(
		"typeIn" => "_in_type",
		"typeNotIn" => "_notin_type",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
	
	
	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
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
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT));
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
