<?php
/**
 * @package plugins.like
 * @subpackage api.filters
 */
class KalturaLikeFilter extends KalturaLikeBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	public function getCoreFilter()
	{
		return new LikeFilter();
	} 
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL2;
	
		$c = new Criteria();
		$c->add(kvotePeer::PARTNER_ID, kCurrentContext::$ks_partner_id);
	
		if($this->entryIdEqual)
				$c->add(kvotePeer::ENTRY_ID, $this->entryIdEqual);
		if($this->userIdEqual)
		{
			$kuser = kuserPeer::getActiveKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $filter->userIdEqual);
			if($kuser)
				$c->add(kvotePeer::KUSER_ID, $kuser->getId());
			else
				$c->add(kvotePeer::PUSER_ID, $this->userIdEqual);
		}
		if($this->createdAtGreaterThanOrEqual)
			$c->add(kvotePeer::CREATED_AT,$this->createdAtGreaterThanOrEqual, Criteria::GREATER_EQUAL);
		if($this->createdAtLessThanOrEqual)
			$c->addAnd(kvotePeer::CREATED_AT,$this->createdAtLessThanOrEqual, Criteria::LESS_EQUAL);
		if($this->entryIdIn)
			$c->add(kvotePeer::ENTRY_ID,explode(',',$this->entryIdIn),Criteria::IN);

		$pager->attachToCriteria($c);
	
		$list = kvotePeer::doSelect($c);
	
		$response = new KalturaLikeListResponse();
		$response->objects = KalturaLikeArray::fromDbArray($list, $responseProfile);
		$response->totalCount = count($list);
		return $response;
	}
	
}
