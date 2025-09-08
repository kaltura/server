<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaEntryVendorTaskFilter extends KalturaEntryVendorTaskBaseFilter
{
	/**
	 * @var string
	 */
	public $freeText;
	
	static private $map_between_objects = array
	(
		"userIdEqual" => "_eq_kuser_id",
		"freeText" => "_free_text",
	);

	static private $order_by_map = array
	(
		"+expectedFinishTime" => "+expected_finish_time",
		"-expectedFinishTime" => "-expected_finish_time",
	);

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function getCoreFilter()
	{
		return new EntryVendorTaskFilter();
	}
	
	
	/* (non-PHPdoc)
 	 * @see KalturaRelatedFilter::getListResponse()
 	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->vendorPartnerIdEqual = kCurrentContext::getCurrentPartnerId();
		
		//Check status filter validity
		if($this->statusEqual || $this->statusIn)
		{
			// only PENDING and SCHEDULED statuses are valid for filtering
			$validStatuses = array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::SCHEDULED);
			$filteredStatus = $this->statusEqual ? array($this->statusEqual) : explode(",", $this->statusIn);
			if (!empty(array_diff($filteredStatus, $validStatuses)))
			{
				KalturaLog::debug("Invalid status filter, defaulting to PENDING");
				$this->statusEqual = EntryVendorTaskStatus::PENDING;
			}
		}
		else
		{
			// default status filter
			KalturaLog::debug("No status filter, defaulting to PENDING");
			$filter->statusEqual = EntryVendorTaskStatus::PENDING;
		}
		
		$c = KalturaCriteria::create(EntryVendorTaskPeer::OM_CLASS);
		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$this->fixFilterUserId($c);
		
		$list = EntryVendorTaskPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		$response = new KalturaEntryVendorTaskListResponse();
		$response->objects = KalturaEntryVendorTaskArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * The user_id is infact a puser_id and the kuser_id should be retrieved
	 */
	private function fixFilterUserId(Criteria $c)
	{
		if ($this->userIdEqual !== null) 
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			if ($kuser)
				$c->add(EntryVendorTaskPeer::KUSER_ID, $kuser->getId());
			else
				$c->add(EntryVendorTaskPeer::KUSER_ID, -1); // no result will be returned when the user is missing
		}
	}
}
