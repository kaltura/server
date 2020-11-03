<?php
/**
 * @package plugins.shortLink
 * @subpackage api.filters
 */
class KalturaShortLinkFilter extends KalturaShortLinkBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new ShortLinkFilter();
	}
	
	public function toFilter($partnerId)
	{
		if(!is_null($this->userIdEqual))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $this->userIdEqual);
			if ($kuser)
				$this->userIdEqual = $kuser->getId();
			else 
				$this->userIdEqual = -1; // no result will be returned when the user is missing
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
				$this->userIdIn = -1; // no result will be returned when the user is missing
			}
		}
	//	$this->partnerIdEqual = $partnerId;

		return parent::toObject();
	}	
}
