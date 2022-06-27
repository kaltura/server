<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaEntryFilter extends KalturaMediaEntryBaseFilter
{
	static private $map_between_objects = array
	(
		"sourceTypeEqual" => "_eq_source",
		"sourceTypeNotEqual" => "_not_source",
		"sourceTypeIn" => "_in_source",
		"sourceTypeNotIn" => "_notin_source",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntryFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager);
		
	    $newList = KalturaMediaEntryArray::fromDbArray($list, $responseProfile);
		$response = new KalturaBaseEntryListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
	
	public function __construct()
	{
		$typeArray = array (entryType::MEDIA_CLIP, entryType::LIVE_STREAM, entryType::LIVE_CHANNEL);
		$typeArray = array_merge($typeArray, KalturaPluginManager::getExtendedTypes(entryPeer::OM_CLASS, entryType::MEDIA_CLIP));
		$typeArray = array_merge($typeArray, KalturaPluginManager::getExtendedTypes(entryPeer::OM_CLASS, entryType::LIVE_STREAM));
		
		$this->typeIn = implode(',', array_unique($typeArray));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new mediaEntryFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		/* @var $object_to_fill mediaEntryFilter */
		if(is_null($object_to_fill))
		{
			$object_to_fill = $this->getCoreFilter();
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
