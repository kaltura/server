<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileNullTokenizer extends Form_Delivery_DeliveryProfileTokenizer
{
	public function init()
	{
 		parent::init();
 		
 		$this->removeElement("key");
 		$this->removeElement("window");
	}
}