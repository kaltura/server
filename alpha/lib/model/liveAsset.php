<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class liveAsset extends flavorAsset
{
	public function getMulticastIP ()
	{
		return $this->getFromCustomData('multicast_ip');
	}
	
	public function setMulticastIP ($v)
	{
		$this->putInCustomData('multicast_ip', $v);
	}
	
	public function getMulticastPort ()
	{
		return $this->getFromCustomData('multicast_port');
	}
	
	public function setMulticastPort ($v)
	{
		$this->putInCustomData('multicast_port', $v);
	}
}
