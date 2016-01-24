<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaQuizUserEntryFilter extends KalturaQuizUserEntryBaseFilter
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $isAnonymous;

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if(!is_null($this->isAnonymous))
		{
			if(KalturaNullableBoolean::toBoolean($this->isAnonymous)===false)
			{
				if($this->userIdNotIn==null)
				{
					$anonKuserIds = "";
					$anonKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), array('', 0));
					foreach ($anonKusers as $anonKuser) {
						$anonKuserIds .= $anonKuser->getKuserId().",";
					}
					$this->userIdNotIn = $anonKuserIds;
				}
			}
		}
		return parent::getListResponse($pager,$responseProfile);
	}
}
