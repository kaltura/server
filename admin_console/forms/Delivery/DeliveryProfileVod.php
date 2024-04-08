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

		$this->addElement('text', 'enforceDeliveriesSupport', array(
			'label'			=> 'Enforce deliveries support:',
		));

		return array('simuliveSupport', 'enforceDeliveriesSupport');
	}

}