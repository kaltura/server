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

	/**
	 * @param KalturaFilterPager $pager
	 * @return KalturaCriteria
	 */
	public function prepareEntriesCriteriaFilter(KalturaFilterPager $pager = null)
	{
		// because by default we will display only READY entries, and when deleted status is requested, we don't want this to disturb
		entryPeer::allowDeletedInCriteriaFilter();

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		if( $this->idEqual == null && $this->redirectFromEntryId == null )
		{
			$this->setDefaultStatus();
			$this->setDefaultModerationStatus($this);
			if(($this->parentEntryIdEqual == null) && ($this->idIn == null) && !$this->isRecordedLiveFilter())
				$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		}

		$this->fixFilterUserId($this);
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$this->toObject($entryFilter);

		if($pager)
		{
			$pager->attachToCriteria($c);
		}

		$entryFilter->attachToCriteria($c);

		return $c;
	}


	protected function isRecordedLiveFilter()
	{
		if(kCurrentContext::$ks_partner_id !== Partner::BATCH_PARTNER_ID)
		{
			return false;
		}


		if($this->sourceTypeEqual && myEntryUtils::isSourceLive($this->sourceTypeEqual))
		{
			return true;
		}

		if($this->sourceTypeIn)
		{
			$sourceTypes = explode(',', $this->sourceTypeIn);
			foreach ($sourceTypes as $sourceType)
			{
				if(myEntryUtils::isSourceLive($sourceType))
				{
					return true;
				}
			}
		}

		return false;
	}
}
