<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaDataCenterContentResource extends KalturaContentResource 
{
	public function getDc()
	{
		return kDataCenterMgr::getCurrentDcId();
	}
	
	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
		
		$dc = $this->getDc();
		if($dc == kDataCenterMgr::getCurrentDcId())
			return;
			
		$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($dc);
		if($remoteDCHost)
			kFile::dumpApiRequest($remoteDCHost);
			
		throw new KalturaAPIException(KalturaErrors::REMOTE_DC_NOT_FOUND, $dc);
	}
}