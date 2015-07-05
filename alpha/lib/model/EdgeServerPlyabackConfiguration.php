<?php

/**
 * Live stream recording entry configuration object 
 * 
 * @package Core
 * @subpackage model
 *
 */
class EdgeServerPlyabackConfiguration
{
	/**
	 * @var boolean
	 */
	protected $multicastEnabled;
	
	/**
	 * @param boolean $shouldCopyEntitlement
	 */
	public function setMulticastEnabled($multicastEnabled)
	{
		$this->multicastEnabled = $multicastEnabled;
	}
	
	/**
	 * @return boolean
	 */
	public function getMulticastEnabled()
	{
		return $this->multicastEnabled;
	}
}