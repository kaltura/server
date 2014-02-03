<?php

/**
 * @package Core
 * @subpackage model
 */
class DeliveryRtmp extends Delivery {

	public function setEnforceRtmpe($v)
	{
		$this->putInCustomData("enforceRtmpe", $v);
	}
	
	public function getEnforceRtmpe()
	{
		return $this->getFromCustomData("enforceRtmpe");
	}
	
	public function setPrefix($v)
	{
		$this->putInCustomData("prefix", $v);
	}
	
	public function getPrefix()
	{
		return $this->getFromCustomData("prefix");
	}
}

