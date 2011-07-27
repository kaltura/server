<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.filters
 */
class KalturaCuePointFilter extends KalturaCuePointBaseFilter
{
	private $map_between_objects = array
	(
		"cuePointTypeEqual" => "_eq_type",
		"cuePointTypeIn" => "_in_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * @param CuePointFilter $cuePointFilter
	 * @param array $propsToSkip
	 * @return CuePointFilter
	 */
	public function toObject($cuePointFilter = null, $propsToSkip = array())
	{
		if(!$cuePointFilter)
			$cuePointFilter = new CuePointFilter();
			
		if(isset($this->userIdEqual)){
			$dbKuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->userIdEqual);
			if (! $dbKuser) {
				throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
			$this->userIdEqual = $dbKuser->getId();
		}
			
		
		if(isset($this->userIdIn)){
			$userIds = explode(",", $this->userIdIn);
			foreach ($userIds as $userId){
				$dbKuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
				if (! $dbKuser) {
				    throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
				$kuserIds = $dbKuser->getId().",";
			}
			
			$this->userIdIn = $kuserIds;
		}
		
		return parent::toObject($cuePointFilter, $propsToSkip);
	}
}
