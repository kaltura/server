<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileVod extends Form_Delivery_DeliveryProfileConfiguration
{

	public function getAdvancedSettings()
	{
		$this->addElement('checkbox', 'simuliveSupport', array(
			'label'			=> 'Simulive support:',
		));

		return array('simuliveSupport');
	}

}