<?php
/**
 * @package plugins.shortLink
 * @subpackage api.filters
 */
class KalturaShortLinkFilter extends KalturaShortLinkBaseFilter
{
	public function toFilter($partnerId)
	{
		$object = new ShortLinkFilter();
		
		if(!is_null($this->userIdEqual))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $filter->userIdEqual);
			if ($kuser)
				$filter->userIdEqual = $kuser->getId();
			else 
				$filter->userIdEqual = -1; // no result will be returned when the user is missing
		}
	
		if(!is_null($this->userIdIn))
		{
			$puserIds = explode(',', $this->userIdIn);
			$kusers = kuserPeer::getKuserByPartnerAndUids($partnerId, $puserIds);
			if(count($kusers))
			{
				$kuserIds = array();
				foreach($kusers as $kuser)
					$kuserIds[] = $kuser->getId();
					
				$this->userIdIn = implode(',', $kuserIds);
			}
			else
			{
				$filter->userIdIn = -1; // no result will be returned when the user is missing
			}
		}

		return parent::toObject($object);
	}	
}
