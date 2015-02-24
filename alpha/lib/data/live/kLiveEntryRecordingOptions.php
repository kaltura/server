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
	 * @return boolean
	 */
	public function getShouldCopyEntitlement()
	{
		return $this->shouldCopyEntitlement;
	}
}