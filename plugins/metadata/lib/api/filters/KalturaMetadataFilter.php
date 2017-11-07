<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters
 */
class KalturaMetadataFilter extends KalturaMetadataBaseFilter
{	
	static private $map_between_objects = array
	(
		"metadataObjectTypeEqual" => "_eq_object_type",
	);

	/* (non-PHPdoc)
	 * @see KalturaMetadataBaseFilter::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * Instantiate default value
	 */
	public function __construct()
	{
		// default value for backward compatibility
		$this->metadataObjectTypeEqual = MetadataObjectType::ENTRY;
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new MetadataFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if (!$this->metadataObjectTypeEqual)
			throw new KalturaAPIException(MetadataErrors::MUST_FILTER_ON_OBJECT_TYPE);
		
		$metadataFilter = $this->toObject();

		$c = KalturaCriteria::create(MetadataPeer::OM_CLASS);
		$metadataFilter->attachToCriteria($c);

		$pager->attachToCriteria($c);
		$list = MetadataPeer::doSelect($c);
		
		$response = new KalturaMetadataListResponse();
		$response->objects = KalturaMetadataArray::fromDbArray($list, $responseProfile);
		
		if($c instanceof SphinxMetadataCriteria)
		{
			$response->totalCount = $c->getRecordsCount();
		}
		elseif($pager->pageIndex == 1 && count($response->objects) < $pager->pageSize)
		{
			$response->totalCount = count($response->objects);
		}
		else
		{
			$pager->detachFromCriteria($c);
			$response->totalCount = MetadataPeer::doCount($c);
		}
		
		return $response;
	}
}
