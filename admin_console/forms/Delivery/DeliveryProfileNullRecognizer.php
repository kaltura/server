<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileNullRecognizer extends Form_Delivery_DeliveryProfileRecognizer
{
	public function init()
	{
 		parent::init();
 		
 		$this->removeElement("hosts");
 		$this->removeElement("uriPrefix");
	}
}