<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaThumbAssetFilter extends KalturaThumbAssetBaseFilter
{	
	/**
	 * @dynamicType KalturaAssetType
	 * @var string
	 */
	public $typeIn = KalturaAssetType::THUMBNAIL;
	
	static private $map_between_objects = array
	(
		"thumbParamsIdEqual" => "_eq_flavor_params_id",
		"thumbParamsIdIn" => "_in_flavor_params_id",
		"typeIn" => "_in_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaThumbAssetListResponse();
		$response->objects = KalturaThumbAssetArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}
}
