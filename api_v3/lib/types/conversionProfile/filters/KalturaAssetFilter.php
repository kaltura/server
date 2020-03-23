<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAssetFilter extends KalturaAssetBaseFilter
{
	/**
	 * @dynamicType KalturaAssetType
	 * @var string
	 */
	public $typeIn;
	
	static private $map_between_objects = array
	(
		"typeIn" => "_in_type",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function validateEntryIdFiltered()
	{
		if(!$this->entryIdEqual && !$this->entryIdIn)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('entryIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('entryIdIn'));
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new AssetFilter();
	}
	
	public function doGetListResponse(KalturaFilterPager $pager, array $types = null)
	{
		$this->validateEntryIdFiltered();
		
	    myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
	    
		// verify access to the relevant entries - either same partner as the KS or kaltura network
		if ($this->entryIdEqual)
		{
			$entryIds = array($this->entryIdEqual);
		}
		else if ($this->entryIdIn)
		{
			$entryIds = explode(',', $this->entryIdIn);
		}
		else
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'KalturaAssetFilter::entryIdEqual/KalturaAssetFilter::entryIdIn');
		}
		
		$entryIds = entryPeer::filterEntriesByPartnerOrKalturaNetwork($entryIds, kCurrentContext::getCurrentPartnerId());
		if (!$entryIds)
		{
			return array(array(), 0);
		}
		
		$this->entryIdEqual = null;
		$this->entryIdIn = implode(',', $entryIds);

		// get the flavors
		$flavorAssetFilter = new AssetFilter();
		
		$this->toObject($flavorAssetFilter);

		$c = new Criteria();
		$flavorAssetFilter->attachToCriteria($c);
		
		if ($flavorAssetFilter->get('_in_type'))
        {
        	//If the $types array is empty we should not return results on the query.
        	$types = array_intersect($types, explode (',', $flavorAssetFilter->get('_in_type')));
        	if(!count($types))
        	{
        		myDbHelper::$use_alternative_con = null;
                return array(array(), 0);
        	}
        }
        
		if($types)
		{
			$c->add(assetPeer::TYPE, $types, Criteria::IN);
		}

		$pager->attachToCriteria($c);
		$list = assetPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = assetPeer::doCount($c);
		}
		
		myDbHelper::$use_alternative_con = null;
		
		return array($list, $totalCount);
	}

	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $types);
		
		$response = new KalturaFlavorAssetListResponse();
		$response->objects = KalturaFlavorAssetArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;  
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		return $this->getTypeListResponse($pager, $responseProfile);  
	}
}
