<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaTranscriptAssetFilter extends KalturaAttachmentAssetFilter
{	
	/**
	 * @dynamicType KalturaAssetType
	 * @var string
	 */
	public $typeIn;

	function __construct()
	{
		$this->typeIn = TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT);
	}
	
	static private $map_between_objects = array("typeIn" => "_in_type");
	static private $order_by_map = array();

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
		$types = $this->typeIn ? explode(",",$this->typeIn) : $types;
		return parent::getTypeListResponse($pager, $responseProfile, $types);
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
