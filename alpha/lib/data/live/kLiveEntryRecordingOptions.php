<?php

/**
 * Live stream recording entry configuration object 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveEntryRecordingOptions
{
	/**
	 * @var boolean
	 */
	protected $shouldCopyEntitlement;
	
	/**
	 * @param boolean $shouldCopyEntitlement
	 */
	public function setShouldCopyEntitlement($shouldCopyEntitlement)
	{
		$this->shouldCopyEntitlement = $shouldCopyEntitlement;
	}
	
	/**
	 * @var int
	 * @return boolean
	 */
	public function getShouldCopyEntitlement($partnerId)
	{
		if ( PermissionPeer::isValidForPartner(PermissionName::FEATURE_LIVE_STREAM_COPY_ENTITELMENTS, $partnerId) )
		{
			return true;
		}
		return $this->shouldCopyEntitlement;
	}
}