<?php
/**
 * @package plugins.schedule
 * @subpackage api.filters
 */
class KalturaScheduleEventFilter extends KalturaScheduleEventBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ScheduleEventFilter();
	}
	
	protected function getListResponseType()
	{
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$type = $this->getListResponseType();
		
		if ($this->ownerIdEqual)
		{
			$dbKuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->ownerIdEqual);
			if (! $dbKuser) {
				throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
			$this->ownerIdEqual = $dbKuser->getId();
		}
		if($this->ownerIdIn){
			$userIds = explode(",", $this->ownerIdIn);
			$dbKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::$ks_partner_id, $userIds);
			if (count($dbKusers) < count($userIds)) {
			    throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
			$kuserIds = array();
			foreach ($dbKusers as $dbKuser){
				$kuserIds[] = $dbKuser->getId();
			}
			
			$this->ownerIdIn = implode(',', $kuserIds);
		}
		
		$c = KalturaCriteria::create(ScheduleEventPeer::OM_CLASS);
		if($type)
		{
			$c->add(ScheduleEventPeer::TYPE, $type);
		}

		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);
			
		$list = ScheduleEventPeer::doSelect($c);
		
		$response = new KalturaScheduleEventListResponse();
		$response->objects = KalturaScheduleEventArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $c->getRecordsCount();
		return $response;
	}
}
