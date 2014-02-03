<?php

/**
 * @package Core
 * @subpackage model
 */
class DeliveryAkamaiHttp extends Delivery {

	public function setUseIntelliseek($v)
	{
		$this->putInCustomData("useIntelliseek", $v);
	}
	public function getUseIntelliseek()
	{
		return $this->getFromCustomData("useIntelliseek");
	}
	
}

