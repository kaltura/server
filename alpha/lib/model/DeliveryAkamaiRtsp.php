<?php

/**
 * @package Core
 * @subpackage model
 */
class DeliveryAkamaiRtsp extends Delivery {

	public function setCpCode($v)
	{
		$this->putInCustomData("cpCode", $v);
	}
	public function getCpCode()
	{
		return $this->getFromCustomData("cpCode");
	}
	
}

