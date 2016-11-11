<?php
/**
 * @package api
 * @subpackage filters
 */
abstract class KalturaRelatedFilter extends KalturaFilter
{
	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 */
	abstract public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null);
	
	public function validateForResponseProfile()
	{
		
	}
	
	protected function preparePusersToKusersFilter( $puserIdsCsv )
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
}
