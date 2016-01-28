<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaQuizUserEntryNoAnonymousUsersFilter extends KalturaQuizUserEntryFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$anonKuserIds = "";
		$anonKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), array('', 0));
		foreach ($anonKusers as $anonKuser) {
			$anonKuserIds .= $anonKuser->getKuserId().",";
		}
		$this->userIdNotIn = $anonKuserIds;
		return parent::getListResponse($pager, $responseProfile); 
	}

}
