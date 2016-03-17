<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserEntryFilter extends KalturaUserEntryBaseFilter
{

	/**
	 * @var KalturaNullableBoolean
	 */
	public $userIdEqualCurrent;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isAnonymous;
	
	/**
	 * @return baseObjectFilter
	 */
	protected function getCoreFilter()
	{
		return new UserEntryFilter();
	}

	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{

		$response = new KalturaUserEntryListResponse();
		if ( in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET)) )
		{
			$response->totalCount = 0;
			return $response;
		}


		$c = new Criteria();
		if (!is_null($this->userIdEqualCurrent) && $this->userIdEqualCurrent)
		{
			$this->userIdEqual = kCurrentContext::getCurrentKsKuserId();
		}
		else
		{
			$this->fixFilterUserId();
		}
		$userEntryFilter = $this->toObject();
		$userEntryFilter->attachToCriteria($c);

		$pager->attachToCriteria($c);
		$list = UserEntryPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && ($resultCount < $pager->pageSize))
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = UserEntryPeer::doCount($c);
		}

		$response->totalCount = $totalCount;
		$response->objects = KalturaUserEntryArray::fromDbArray($list, $responseProfile);
		return $response;
	}

	private function preparePusersToKusersFilter( $puserIdsCsv )
	{
		$kuserIdsArr = array();
		$puserIdsArr = explode(',',$puserIdsCsv);
		$kuserArr = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), $puserIdsArr);

		foreach($kuserArr as $kuser)
		{
			$kuserIdsArr[] = $kuser->getId();
		}

		if(!empty($kuserIdsArr))
		{
			return implode(',',$kuserIdsArr);
		}

		return -1; // no result will be returned if no puser exists
	}

	/**
	 * The user_id is infact a puser_id and the kuser_id should be retrieved
	 */
	protected function fixFilterUserId()
	{
		if ($this->userIdEqual !== null)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			if ($kuser)
				$this->userIdEqual = $kuser->getId();
			else
				$this->userIdEqual = -1; // no result will be returned when the user is missing
		}

		if(!empty($this->userIdIn))
		{
			$this->userIdIn = $this->preparePusersToKusersFilter( $this->userIdIn );
		}
		if(!empty($this->userIdNotIn))
		{
			$this->userIdNotIn = $this->preparePusersToKusersFilter( $this->userIdNotIn );
		}

		if(!is_null($this->isAnonymous))
		{
			if(KalturaNullableBoolean::toBoolean($this->isAnonymous)===false)
				$this->userIdNotIn .= self::getListOfAnonymousUsers();

			elseif(KalturaNullableBoolean::toBoolean($this->isAnonymous)===true)
				$this->userIdIn .= self::getListOfAnonymousUsers();
		}
	}

	public static function getListOfAnonymousUsers()
	{
		$anonKuserIds = "";
		$anonKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), array(0,''));
		foreach ($anonKusers as $anonKuser) {
			$anonKuserIds .= ",".$anonKuser->getKuserId();
		}
		return $anonKuserIds;
	}
}
