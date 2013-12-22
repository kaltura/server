<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaDataCenterContentResource extends KalturaContentResource 
{
	public function getDc()
	{
		return kDataCenterMgr::getCurrentDcId();
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);
		
		$dc = $this->getDc();
		if($dc == kDataCenterMgr::getCurrentDcId())
			return;
			
		$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($dc);
		if($remoteDCHost)
			kFileUtils::dumpApiRequest($remoteDCHost);
			
		throw new KalturaAPIException(KalturaErrors::REMOTE_DC_NOT_FOUND, $dc);
	}
}