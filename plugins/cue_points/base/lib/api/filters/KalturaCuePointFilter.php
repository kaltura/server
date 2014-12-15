<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.filters
 */
class KalturaCuePointFilter extends KalturaCuePointBaseFilter
{
	/**
	 * @var string
	 */
	public $freeText;
	
	static private $map_between_objects = array
	(
		"cuePointTypeEqual" => "_eq_type",
		"cuePointTypeIn" => "_in_type",
		"freeText" => "_free_text",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function validateEntryIdFiltered()
	{
		if(!$this->idEqual && !$this->idIn && !$this->entryIdEqual && !$this->entryIdIn)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL,
					$this->getFormattedPropertyNameWithClassName('idEqual') . '/' . $this->getFormattedPropertyNameWithClassName('idIn') . '/' .
					$this->getFormattedPropertyNameWithClassName('entryIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('entryIdIn'));
	}

	/**
	 * @param CuePointFilter $cuePointFilter
	 * @param array $propsToSkip
	 * @return CuePointFilter
	 */
	public function toObject($cuePointFilter = null, $propsToSkip = array())
	{
		$this->validateEntryIdFiltered();
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
